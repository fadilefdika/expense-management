@props([
    'headers' => [],
    'rows' => [],
    'monthlyTotals' => [],
    'expenseTypes' => [],
    'highchartsSeries' => [],
    'highchartsDrill' => [],
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
    <!-- Header & Toggle -->
    <div class="d-flex justify-content-between align-items-center px-3 pt-3">
        <h3 class="mb-0" style="font-size: 1.125rem; font-weight: 600;">{{ $title }}</h3>
        <div class="btn-group shadow-sm rounded overflow-hidden" role="group" style="font-size: 12px;">
            <button type="button" class="filter-toggle btn btn-md btn-outline-primary active d-flex align-items-center gap-1 px-3" id="tableViewBtn">
                <i class="bi bi-table"></i>
                <span>Table</span>
            </button>
            <button type="button" class="filter-toggle btn btn-md btn-outline-primary d-flex align-items-center gap-1 px-3" id="chartViewBtn">
                <i class="bi bi-bar-chart"></i>
                <span>Chart</span>
            </button>
        </div>
        
    </div>

    <!-- Only show on Table View -->
    <div id="tableExtras" class="px-3">
        <!-- Filters -->
        <div class="d-flex align-items-end gap-2 mt-3" style="font-size: 10px;">
            <!-- Tahun -->
            <div>
                <label for="yearSelector" class="form-label mb-1" style="font-size: 10px;">Tahun</label>
                <select id="yearSelector" class="form-select form-select-sm" style="width: 100px; font-size: 10px;">
                    @foreach(range(date('Y') - 5, date('Y') + 1) as $year)
                        <option value="{{ $year }}" {{ $year == date('Y') ? 'selected' : '' }}>{{ $year }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Bulan Awal -->
            <div>
                <label for="startMonthSelector" class="form-label mb-1" style="font-size: 10px;">Bulan Awal</label>
                <select id="startMonthSelector" class="form-select form-select-sm" style="width: 120px; font-size: 10px;">
                    <option value="">Pilih Bulan Awal</option>
                    @foreach($headers as $index => $month)
                        <option value="{{ $index }}" {{ $index == 0 ? 'selected' : '' }}>{{ $month }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Bulan Akhir -->
            <div>
                <label for="endMonthSelector" class="form-label mb-1" style="font-size: 10px;">Bulan Akhir</label>
                <select id="endMonthSelector" class="form-select form-select-sm" style="width: 120px; font-size: 10px;">
                    <option value="">Pilih Bulan Akhir</option>
                    @foreach($headers as $index => $month)
                        <option value="{{ $index }}" {{ $index == 11 ? 'selected' : '' }}>{{ $month }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Tombol Apply -->
            <div class="pt-2 d-flex gap-1">
                <button type="button" onclick="applyMonthRange()" class="btn btn-sm btn-primary" style="font-size: 10px;">
                    Apply
                </button>
                <button type="button" onclick="resetMonthRange()" class="btn btn-sm btn-secondary" style="font-size: 10px;">
                    Reset
                </button>
            </div>
        </div>


        <!-- Search & Dropdown -->
        <div class="d-flex align-items-center gap-2 mt-3 mb-2" style="font-size: 10px;">
            <!-- Search Input -->
            <div class="position-relative" style="width: 200px;">
                <input type="text" class="form-control form-control-sm ps-4" placeholder="Search..." id="searchInput" style="font-size: 10px;">
                <i class="bi bi-search position-absolute" 
                style="top: 50%; left: 10px; transform: translateY(-50%); font-size: 12px; color: #888;"></i>
            </div>

            <!-- Dropdown Filter -->
            @if(count($expenseTypes))
            <div style="width: 160px;">
                <select class="form-select form-select-sm" id="typeFilter" style="font-size: 10px;">
                    <option value="all">All Types</option>
                    @foreach($expenseTypes as $type)
                        <option value="{{ $type }}">{{ $type }}</option>
                    @endforeach
                </select>
            </div>
            @endif
        </div>

        <!-- Summary Cards -->
        <div class="d-flex gap-3 flex-wrap mb-3">
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
                    <div class="card-value">{{ number_format($highestMonthTotal, 0, ',', '.') }} 
                        <span class="card-subtext">({{ $highestMonth }})</span>
                    </div>
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
    </div>

    <!-- Table View -->
    <div id="tableView" class="px-3">
        <div class="d-flex justify-content-between align-items-center px-3 my-2">
            <div>
                <label for="rowsPerPage" class="me-2" style="font-size: 12px;">Rows per page:</label>
                <select id="rowsPerPage" class="form-select form-select-sm d-inline-block" style="width: auto;">
                    <option value="10">10</option>
                    <option value="25" selected>25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            </div>
            <div id="paginationControls" style="font-size: 12px;"></div>
        </div>
        
        <div class="notion-table-container">
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
                        <tr class="notion-table-row" data-months="{{ implode(',', $row['monthly']) }}">
                            <td style="font-size: 9px;">{{ $row['expense_type'] ?? $row['vendor'] ?? '-' }}</td>
                            @if(isset($rows[0]['category']))
                                <td style="font-size: 9px;">{{ $row['category'] ?? '-' }}</td>
                            @endif
                            @foreach($row['monthly'] as $val)
                                <td style="font-size: 9px;">{{ number_format($val, 0, ',', '.') }}</td>
                            @endforeach
                            <td style="font-size: 9px;">{{ number_format($row['total'], 0, ',', '.') }}</td>
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

    <!-- Chart View -->
    <div id="chartView" class="d-none px-3 pb-3">
        <div id="expenseChart" style="min-width: 800px; height: 400px;"></div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const tableBody = document.getElementById("tableBody");
        const rows = tableBody.querySelectorAll("tr");
        const paginationControls = document.getElementById("paginationControls");
        const rowsPerPageSelect = document.getElementById("rowsPerPage");

        let currentPage = 1;
        let rowsPerPage = parseInt(rowsPerPageSelect.value);

        function renderTable() {
            const totalPages = Math.ceil(rows.length / rowsPerPage);
            const start = (currentPage - 1) * rowsPerPage;
            const end = start + rowsPerPage;

            rows.forEach((row, index) => {
                row.style.display = (index >= start && index < end) ? "" : "none";
            });

            renderPagination(totalPages);
        }

        function renderPagination(totalPages) {
            let html = `<span class="me-2">Page ${currentPage} of ${totalPages}</span>`;
            html += `<button class="btn btn-sm btn-outline-secondary me-1" ${currentPage === 1 ? 'disabled' : ''} onclick="goToPage(${currentPage - 1})">Previous</button>`;
            html += `<button class="btn btn-sm btn-outline-secondary" ${currentPage === totalPages ? 'disabled' : ''} onclick="goToPage(${currentPage + 1})">Next</button>`;
            paginationControls.innerHTML = html;
        }

        window.goToPage = function (page) {
            currentPage = page;
            renderTable();
        }

        rowsPerPageSelect.addEventListener("change", function () {
            rowsPerPage = parseInt(this.value);
            currentPage = 1;
            renderTable();
        });

        renderTable();
    });
</script>


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

        updateTableFooter(monthlyTotals, grandTotal);

        // Sinkronkan filter agar tetap akurat
        filterTable();
    }

    function resetMonthRange() {
        // Reset ke nilai default (bulan awal: 0, bulan akhir: 11)
        startMonthSelector.value = "0";
        endMonthSelector.value = "11";

        const hasCategory = @json(isset($rows[0]['category']));
        const rows = document.querySelectorAll('#tableBody tr');
        const footerRow = document.querySelector('tfoot tr');
        const headerRow = document.querySelector('thead tr');

        const monthlyTotals = new Array(12).fill(0);
        let grandTotal = 0;

        rows.forEach(row => {
            // Tampilkan semua kolom bulanan
            const rowTotal = updateRow(row, 0, 11, monthlyTotals, hasCategory);
            grandTotal += rowTotal;
        });

        updateHeader(headerRow, 0, 11, hasCategory);
        updateFooter(footerRow, monthlyTotals, grandTotal, 0, 11, hasCategory);
        updateFooterVisibility(footerRow, 0, 11, hasCategory);

        updateTableFooter(monthlyTotals, grandTotal);

        // Jalankan filter ulang jika ada input pencarian / filter type
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
            document.getElementById('tableExtras').classList.add('d-none');
            chartBtn.classList.add('active');
            tableBtn.classList.remove('active');
            drawChart();
        });

        tableBtn?.addEventListener('click', () => {
            chartView.classList.add('d-none');
            tableView.classList.remove('d-none');

            tableBtn.classList.add('active');
            document.getElementById('tableExtras').classList.remove('d-none');
            chartBtn.classList.remove('active');
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
    const chartData = {
        series: @json($highchartsSeries),
        drilldown: @json($highchartsDrill)
    };
    console.log(chartData);
</script>



<script>
    function drawChart() {
        Highcharts.chart('expenseChart', {
            chart: {
                type: 'column'
            },
            title: {
                text: 'Expense Type Overview',
                style: {
                    fontSize: '16px'
                }
            },
            xAxis: {
                type: 'category',
                labels: {
                    rotation: -45,
                    style: {
                        fontSize: '6px',
                        fontWeight: 'normal'
                    }
                }
            },
            yAxis: {
                title: {
                    text: 'Total Expense (Rp)'
                }
            },
            legend: {
                enabled: false
            },
            plotOptions: {
                series: {
                    borderWidth: 0,
                    dataLabels: {
                        enabled: true,
                        format: 'Rp{point.y:,.0f}',
                        style: {
                            fontSize: '10px',
                            fontWeight: 'bold',
                            color: '#0033cc'
                        }
                    }
                }
            },
            tooltip: {
                headerFormat: '<span>{series.name}</span><br>',
                pointFormat: '<span>{point.name}</span>: <b>Rp{point.y:,.0f}</b><br/>'
            },
            series: [{
                name: 'Expense Type',
                colorByPoint: true,
                data: chartData.series
            }],
            drilldown: {
                series: chartData.drilldown
            }
        });
    }
</script>

