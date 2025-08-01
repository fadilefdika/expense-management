@props([
    'headers' => [],
    'rows' => [],
    'monthlyTotals' => [],
    'expenseTypes' => [],
    'title' => 'Report Table',
    'label1' => 'Expense Type',
    'label2' => 'Expense Category',
    'idPrefix' => 'report',
])

@php
    $totalSum = array_sum($monthlyTotals);
    $average = count($monthlyTotals) ? $totalSum / count($monthlyTotals) : 0;
    $highestMonthTotal = max($monthlyTotals);
    $highestMonth = $headers[array_search($highestMonthTotal, $monthlyTotals)] ?? 'N/A';
@endphp

<div class="notion-report-container bg-white border rounded-3" id="reportApp">
    <!-- Header Section -->
    <div class="notion-header">
        <div class="d-flex flex-column">
            <h3 style="font-size: 1.125rem; font-weight: 600;">{{ $title }}</h3>
        </div>
    </div>

    <!-- Filters Section - Horizontal -->
    <div class="filter-container">
        <!-- Search Input -->
        <div class="filter-search">
            <i class="bi bi-search filter-icon"></i>
            <input type="text" class="filter-input" placeholder="Search..." id="searchInput">
        </div>

        <!-- Dropdown Filter -->
        @if(count($expenseTypes))
        <div class="filter-select">
            <select class="filter-dropdown" id="typeFilter">
                <option value="all">All Types</option>
                @foreach($expenseTypes as $type)
                    <option value="{{ $type }}">{{ $type }}</option>
                @endforeach
            </select>
            <i class="bi bi-chevron-down dropdown-arrow"></i>
        </div>
        @endif

        <!-- Toggle Buttons -->
        <div class="filter-toggle-group">
            <button type="button" class="filter-toggle active" id="tableViewBtn">
                <i class="bi bi-table"></i>
                <span>Table</span>
            </button>
            <button type="button" class="filter-toggle" id="chartViewBtn">
                <i class="bi bi-bar-chart"></i>
                <span>Chart</span>
            </button>
        </div>
    </div>


    <!-- Summary Cards -->
    <div class="notion-summary-cards">
        <div class="notion-summary-card">
            <div class="card-icon bg-blue"><i class="bi bi-cash-stack"></i></div>
            <div>
                <div class="card-label">Total Expenses</div>
                <div class="card-value">{{ number_format($totalSum, 0, ',', '.') }}</div>
            </div>
        </div>
        <div class="notion-summary-card">
            <div class="card-icon bg-green"><i class="bi bi-graph-up-arrow"></i></div>
            <div>
                <div class="card-label">Highest Month</div>
                <div class="card-value">{{ number_format($highestMonthTotal, 0, ',', '.') }} <span class="card-subtext">({{ $highestMonth }})</span></div>
            </div>
        </div>
        <div class="notion-summary-card">
            <div class="card-icon bg-purple"><i class="bi bi-calendar-check"></i></div>
            <div>
                <div class="card-label">Average Monthly</div>
                <div class="card-value">{{ number_format($average, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>

    <!-- Chart View -->
    <div id="chartView" class="notion-chart-container d-none">
        <canvas id="{{ $idPrefix }}-chart"></canvas>
    </div>

    <!-- Table View -->
    <div class="filter-month-year mb-3 d-flex align-items-center gap-2">
        <select id="yearSelector" class="form-select form-select-sm" style="width: 100px;">
            @foreach(range(date('Y') - 5, date('Y') + 1) as $year)
                <option value="{{ $year }}" {{ $year == date('Y') ? 'selected' : '' }}>{{ $year }}</option>
            @endforeach
        </select>
        
        <select id="startMonthSelector" class="form-select form-select-sm" style="width: 120px;">
            <option value="">Pilih Bulan Awal</option>
            @foreach($headers as $index => $month)
                <option value="{{ $index }}" {{ $index == 0 ? 'selected' : '' }}>{{ $month }}</option>
            @endforeach
        </select>
        
        <select id="endMonthSelector" class="form-select form-select-sm" style="width: 120px;">
            <option value="">Pilih Bulan Akhir</option>
            @foreach($headers as $index => $month)
                <option value="{{ $index }}" {{ $index == 11 ? 'selected' : '' }}>{{ $month }}</option>
            @endforeach
        </select>
        
        <button type="button" onclick="applyMonthRange()" class="btn btn-sm btn-primary">
            Apply
        </button>
    </div>
    
    <div id="tableView" class="notion-table-container">
        <table class="notion-table w-100">
            <thead>
                <tr>
                    <th style="font-size: 10px;">{{ $label1 }}</th>
                    @if(isset($rows[0]['category']))
                        <th style="font-size: 10px;">{{ $label2 }}</th>
                    @endif
                    @foreach($headers as $header)
                        <th style="font-size: 10px;">{{ $header }}</th>
                    @endforeach
                    <th style="font-size: 10px;">Total</th>
                </tr>
            </thead>
            <tbody id="tableBody">
                @foreach($rows as $row)
                    @php
                        $monthly = $row['monthly'];
                    @endphp
                    <tr class="notion-table-row" data-months="{{ implode(',', $monthly) }}">
                        <td style="font-size: 8px;">{{ $row['expense_type'] ?? $row['vendor'] ?? '-' }}</td>
                        @if(isset($rows[0]['category']))
                            <td style="font-size: 8px;">{{ $row['category'] ?? '-' }}</td>
                        @endif
                        @foreach($monthly as $val)
                            <td style="font-size: 9px;">{{ number_format($val, 0, ',', '.') }}</td>
                        @endforeach
                        <td class="row-total" style="font-size: 9px;">{{ number_format($row['total'], 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="notion-total-row">
                    <td>Grand Total</td>
                    @if(isset($rows[0]['category']))
                        <td></td>
                    @endif
                    @foreach($monthlyTotals as $total)
                        <td>{{ number_format($total, 0, ',', '.') }}</td>
                    @endforeach
                    <td>{{ number_format($totalSum, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>


<script>
    // =================== APLIKASI RANGE BULAN ===================
    function applyMonthRange() {
        const startIndex = parseInt(startMonthSelector.value);
        const endIndex = parseInt(endMonthSelector.value);

        if (isNaN(startIndex) || isNaN(endIndex)) {
            alert("Silakan pilih bulan awal dan akhir");
            return;
        }

        if (startIndex > endIndex) {
            alert("Bulan akhir tidak boleh sebelum bulan awal");
            return;
        }

        const hasCategory = @json(isset($rows[0]['category']));
        const rows = document.querySelectorAll('#tableBody tr');
        const footerRow = document.querySelector('tfoot tr');
        const headerRow = document.querySelector('thead tr');

        const monthlyTotals = new Array(12).fill(0);
        let grandTotal = 0;

        rows.forEach(row => {
            const rowTotal = updateRow(row, startIndex, endIndex, monthlyTotals, hasCategory);
            grandTotal += rowTotal;
        });

        updateHeader(headerRow, startIndex, endIndex, hasCategory);
        updateFooter(footerRow, monthlyTotals, grandTotal, startIndex, endIndex, hasCategory);
        updateFooterVisibility(footerRow, startIndex, endIndex, hasCategory);

        console.log('applyMonthRange monthlyTotals', monthlyTotals);
        console.log('applyMonthRange grandTotal', grandTotal);

        updateTableFooter(monthlyTotals, grandTotal);

        // Sinkronkan filter agar tetap akurat
        filterTable();
    }

    // =================== PENGATURAN TIAP ROW ===================
    function updateRow(row, start, end, monthlyTotals, hasCategory) {
        const cells = row.querySelectorAll('td');
        const monthlyData = row.dataset.months.split(',').map(Number);

        const monthCells = hasCategory
            ? Array.from(cells).slice(2, -1)
            : Array.from(cells).slice(1, -1);

        let rowTotal = 0;

        monthCells.forEach((cell, index) => {
            const active = index >= start && index <= end;
            const value = active ? (monthlyData[index] || 0) : 0;

            cell.style.display = active ? '' : 'none';
            cell.textContent = new Intl.NumberFormat('id-ID').format(value);

            if (active) {
                rowTotal += value;
                monthlyTotals[index] += value;
            }
        });

        cells[cells.length - 1].textContent = new Intl.NumberFormat('id-ID').format(rowTotal);
        return rowTotal;
    }

    // =================== HEADER & FOOTER ===================
    function updateHeader(headerRow, start, end, hasCategory) {
        if (!headerRow) return;
        const cells = headerRow.querySelectorAll('th');

        const monthCells = hasCategory
            ? Array.from(cells).slice(2, -1)
            : Array.from(cells).slice(1, -1);

        monthCells.forEach((cell, index) => {
            cell.style.display = (index >= start && index <= end) ? '' : 'none';
        });
    }

    function updateFooter(footerRow, monthlyTotals, grandTotal, start, end, hasCategory) {
        if (!footerRow) return;
        const cells = footerRow.querySelectorAll('td');

        const monthCells = hasCategory
            ? Array.from(cells).slice(2, -1)
            : Array.from(cells).slice(1, -1);

        monthCells.forEach((cell, index) => {
            const value = (index >= start && index <= end) ? monthlyTotals[index] : 0;
            cell.textContent = new Intl.NumberFormat('id-ID').format(value);
        });

        cells[cells.length - 1].textContent = new Intl.NumberFormat('id-ID').format(grandTotal);
    }

    function updateFooterVisibility(footerRow, start, end, hasCategory) {
        if (!footerRow) return;
        const cells = footerRow.querySelectorAll('td');

        const monthCells = hasCategory
            ? Array.from(cells).slice(2, -1)
            : Array.from(cells).slice(1, -1);

        monthCells.forEach((cell, index) => {
            cell.style.display = (index >= start && index <= end) ? '' : 'none';
        });
    }

    function updateTableFooter(monthlyTotals, grandTotal) {
        const tfoot = document.querySelector('tfoot');
        if (!tfoot) return;

        const row = tfoot.querySelector('tr');
        const cells = row.children;

        for (let i = 0; i < 12; i++) {
            cells[i + 2].textContent = new Intl.NumberFormat('id-ID').format(monthlyTotals[i]);
        }

        cells[14].textContent = new Intl.NumberFormat('id-ID').format(grandTotal);
    }

    // =================== UTILITAS ===================
    function getVisibleRowTotal(cells) {
        let total = 0;
        for (let i = 2; i <= 13; i++) {
            if (cells[i].style.display === 'none') continue;
            const value = parseInt(cells[i].textContent.replace(/\./g, '')) || 0;
            total += value;
        }
        return total;
    }
    function filterTable() {
    const search = document.getElementById('searchInput')?.value.toLowerCase() || '';
    const type = document.getElementById('typeFilter')?.value || 'all';

    const tableBody = document.getElementById('tableBody');
    const allRows = [...tableBody.querySelectorAll('tr')];

    const visibleRows = [];
    let grandTotal = 0;
    const monthlyTotals = new Array(12).fill(0); // Untuk Jan - Dec

    allRows.forEach(row => {
        const cells = row.children;
        const typeCell = cells[0].textContent.toLowerCase();
        const categoryCell = cells[1]?.textContent.toLowerCase() || '';
        const matchesSearch = typeCell.includes(search) || categoryCell.includes(search);
        const matchesType = type === 'all' || typeCell === type.toLowerCase();

        const isVisible = matchesSearch && matchesType;
        row.style.display = isVisible ? '' : 'none';

        if (isVisible) {
            visibleRows.push(row);

            // Hanya hitung kolom bulan yang terlihat
            for (let i = 2; i <= 13; i++) {
                if (cells[i].style.display === 'none') continue;
                const value = parseInt(cells[i].textContent.replace(/\./g, '')) || 0;
                monthlyTotals[i - 2] += value;
            }

            // Kolom total (index 14)
            const totalValue = parseInt(cells[14].textContent.replace(/\./g, '')) || 0;
            grandTotal += totalValue;
        }
    });

    console.log('filter table monthlyTotals', monthlyTotals);
    console.log('filter table grandTotal', grandTotal);

    updateTableFooter(monthlyTotals, grandTotal);
}

    // =================== EVENT LISTENER ===================
    document.addEventListener('DOMContentLoaded', () => {
        const searchInput = document.getElementById('searchInput');
        const typeFilter = document.getElementById('typeFilter');
        const tableBody = document.getElementById('tableBody');
        const allRows = [...tableBody.querySelectorAll('tr')];

        const chartBtn = document.getElementById('chartViewBtn');
        const tableBtn = document.getElementById('tableViewBtn');
        const chartView = document.getElementById('chartView');
        const tableView = document.getElementById('tableView');

        const startMonthSelector = document.getElementById('startMonthSelector');
        const endMonthSelector = document.getElementById('endMonthSelector');
        const yearSelector = document.getElementById('yearSelector');

        // Filter realtime
        searchInput?.addEventListener('input', filterTable);
        typeFilter?.addEventListener('change', filterTable);

        // Toggle view
        chartBtn?.addEventListener('click', () => {
            chartView.classList.remove('d-none');
            tableView.classList.add('d-none');
        });

        tableBtn?.addEventListener('click', () => {
            chartView.classList.add('d-none');
            tableView.classList.remove('d-none');
        });

        // Filter end month jika start berubah
        startMonthSelector?.addEventListener('change', function () {
            const startIndex = parseInt(this.value);
            endMonthSelector.disabled = isNaN(startIndex);

            if (!isNaN(startIndex)) {
                Array.from(endMonthSelector.options).forEach(option => {
                    if (option.value === "") return;
                    const monthIndex = parseInt(option.value);
                    option.disabled = monthIndex < startIndex;

                    if (endMonthSelector.value && parseInt(endMonthSelector.value) < startIndex) {
                        endMonthSelector.value = "";
                    }
                });
            }
        });

        // Inisialisasi
        startMonthSelector.dispatchEvent(new Event('change'));
        applyMonthRange();
    });
</script>


<script>
    const ctx = document.getElementById('{{ $idPrefix }}-chart')?.getContext('2d');
    if (ctx) {
        const rawRows = @json($rows);
        const headers = @json($headers);

        const groupedData = {};

        rawRows.forEach(row => {
            const label = row.expense_type || row.vendor || 'Other';
            const monthly = Array.isArray(row.monthly) ? row.monthly : [];

            if (!groupedData[label]) {
                groupedData[label] = new Array(headers.length).fill(0);
            }

            monthly.forEach((val, idx) => {
                groupedData[label][idx] += val;
            });
        });

        const colors = [
            'rgba(51, 126, 169, 0.7)',
            'rgba(45, 157, 120, 0.7)',
            'rgba(144, 101, 176, 0.7)',
            'rgba(212, 76, 71, 0.7)',
            'rgba(230, 154, 84, 0.7)',
            'rgba(155, 154, 151, 0.7)'
        ];

        const datasets = Object.entries(groupedData).map(([label, data], i) => ({
            label,
            data,
            backgroundColor: colors[i % colors.length],
            borderColor: colors[i % colors.length].replace('0.7', '1'),
            borderWidth: 1,
            borderRadius: 2
        }));

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: headers,
                datasets
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        stacked: true,
                        grid: { display: false },
                        ticks: { font: { size: 11 } }
                    },
                    y: {
                        stacked: true,
                        beginAtZero: true,
                        ticks: {
                            callback: value => value >= 1000 ? (value / 1000) + 'k' : value,
                            font: { size: 11 }
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: context => `${context.dataset.label || ''}: ${new Intl.NumberFormat().format(context.raw)}`
                        }
                    },
                    legend: {
                        position: 'bottom',
                        labels: { boxWidth: 10, padding: 12, font: { size: 11 } }
                    }
                },
                interaction: {
                    mode: 'index',
                    intersect: false
                }
            }
        });
    }
</script>

