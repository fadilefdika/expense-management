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
    @php
        $currentYear = date('Y');
    @endphp

    <div class="d-flex flex-wrap align-items-center gap-2 mb-3" style="font-size: 10px;">
        <!-- Year -->
        <select id="yearFilter" class="form-select form-select-sm" style="width: 100px;">
            @for ($y = $currentYear - 2; $y <= $currentYear + 2; $y++)
                <option value="{{ $y }}" @if($y == $currentYear) selected @endif>{{ $y }}</option>
            @endfor
        </select>

        <!-- Month From -->
        <select id="monthFrom" class="form-select form-select-sm" style="width: 120px;">
            @foreach(['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'] as $index => $month)
                <option value="{{ $index }}">{{ $month }}</option>
            @endforeach
        </select>

        <!-- Month To -->
        <select id="monthTo" class="form-select form-select-sm" style="width: 120px;">
            @foreach(['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'] as $index => $month)
                <option value="{{ $index }}" @if($index == 11) selected @endif>{{ $month }}</option>
            @endforeach
        </select>

        <!-- Button -->
        <button onclick="applyMonthFilter()" class="btn btn-sm btn-outline-primary">Apply Filter</button>
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
            @php
                $showCategory = isset($rows[0]['category']);
            @endphp

            <tbody id="tableBody">
                @foreach($rows as $row)
                    @php
                        $monthly = $row['monthly'];
                        $rowName = $row['expense_type'] ?? $row['vendor'] ?? '-';
                        $rowCategory = $row['category'] ?? '-';
                    @endphp
                    <tr class="notion-table-row" data-months="{{ implode(',', $monthly) }}">
                        <td class="text-xs">{{ $rowName }}</td>
                        @if($showCategory)
                            <td class="text-xs">{{ $rowCategory }}</td>
                        @endif
                        @foreach($monthly as $val)
                            <td class="monthly-cell text-xs">{{ number_format($val, 0, ',', '.') }}</td>
                        @endforeach
                        <td class="row-total text-xs">{{ number_format($row['total'], 0, ',', '.') }}</td>
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
    document.addEventListener('DOMContentLoaded', () => {
        const searchInput = document.getElementById('searchInput');
        const typeFilter = document.getElementById('typeFilter');
        const tableBody = document.getElementById('tableBody');
        const allRows = [...tableBody.querySelectorAll('tr')];

        const tableView = document.getElementById('tableView');
        const chartView = document.getElementById('chartView');
        const chartBtn = document.getElementById('chartViewBtn');
        const tableBtn = document.getElementById('tableViewBtn');

        function filterTable() {
            const search = searchInput.value.toLowerCase();
            const type = typeFilter?.value || 'all';

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
                const fromMonth = parseInt(document.getElementById('monthFrom').value);
                const toMonth = parseInt(document.getElementById('monthTo').value);


                if (isVisible) {
                    visibleRows.push(row);

                    const monthlyValues = row.dataset.months.split(',').map(v => parseInt(v));
                    let rowTotal = 0;

                    for (let i = fromMonth; i <= toMonth; i++) {
                        const val = monthlyValues[i] || 0;
                        rowTotal += val;
                        monthlyTotals[i] += val;

                        // Update sel bulan ke-i (kolom bulan mulai dari index 2 atau 3)
                        const monthCellIndex = cells.length === 14 ? i + 2 : i + 3;
                        if (cells[monthCellIndex]) {
                            cells[monthCellIndex].textContent = new Intl.NumberFormat('id-ID').format(val);
                        }
                    }

                    // Update total kolom terakhir
                    const totalCell = row.querySelector('.row-total');
                    totalCell.textContent = new Intl.NumberFormat('id-ID').format(rowTotal);

                    grandTotal += rowTotal;
                }

            });

            updateTableFooter(monthlyTotals, grandTotal);
        }


        function updateTableFooter(monthlyTotals, grandTotal) {
            const tfoot = document.querySelector('tfoot');
            if (!tfoot) return;

            const row = tfoot.querySelector('tr');
            const cells = row.children;

            // Kolom bulan: index 2 sampai 13
            for (let i = 0; i < 12; i++) {
                cells[i + 2].textContent = new Intl.NumberFormat('id-ID').format(monthlyTotals[i]);
            }

            // Kolom total (index 14)
            cells[14].textContent = new Intl.NumberFormat('id-ID').format(grandTotal);
        }




        searchInput?.addEventListener('input', filterTable);
        typeFilter?.addEventListener('change', filterTable);

        chartBtn?.addEventListener('click', () => {
            chartView.classList.remove('d-none');
            tableView.classList.add('d-none');
        });

        tableBtn?.addEventListener('click', () => {
            chartView.classList.add('d-none');
            tableView.classList.remove('d-none');
        });

        filterTable();
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

<script>
    function applyMonthFilter() {
        const from = parseInt(document.getElementById('monthFrom').value);
        const to = parseInt(document.getElementById('monthTo').value);
    
        // Validasi
        if (from > to) {
            alert("Bulan awal tidak boleh lebih besar dari bulan akhir.");
            return;
        }
    
        const table = document.querySelector('.notion-table');
        const rows = table.querySelectorAll('tr');
    
        rows.forEach(row => {
            const cells = Array.from(row.children);
            
            // Mulai dari kolom bulan = index ke-2 atau 3 (karena kolom awal = type + optional category)
            const offset = (cells.length === 15) ? 2 : 1; // 2 = ada kategori, 1 = tidak
    
            for (let i = 0; i < 12; i++) {
                const cell = cells[i + offset];
                if (!cell) continue;
    
                if (i >= from && i <= to) {
                    cell.style.display = '';
                } else {
                    cell.style.display = 'none';
                }
            }
        });
    
        // Optional: scroll to table view
        document.getElementById('tableView').scrollIntoView({ behavior: 'smooth' });
    }
</script>
    
<script>
    const monthFrom = document.getElementById('monthFrom');
    const monthTo = document.getElementById('monthTo');
    
    monthFrom.addEventListener('change', function () {
        const fromIndex = parseInt(this.value);
        const monthNames = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    
        monthTo.innerHTML = '';
    
        for (let i = fromIndex; i < 12; i++) {
            const option = document.createElement('option');
            option.value = i;
            option.textContent = monthNames[i];
            monthTo.appendChild(option);
        }
    
        monthTo.selectedIndex = monthTo.options.length - 1;
    });
    
    // Initialize on page load
    monthFrom.dispatchEvent(new Event('change'));
</script>
    