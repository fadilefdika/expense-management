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
            <div class="d-flex gap-2 mt-1">
                <span class="badge bg-secondary" style="font-size: 10px;">{{ count($rows) }} items</span>
                <span class="badge bg-secondary" style="font-size: 10px;">{{ number_format($totalSum, 0, ',', '.') }} total</span>
            </div>
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
                    <tr class="notion-table-row">
                        <td style="font-size: 8px;">{{ $row['expense_type'] ?? $row['vendor'] ?? '-' }}</td>
                        @if(isset($rows[0]['category']))
                            <td style="font-size: 8px;">{{ $row['category'] ?? '-' }}</td>
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



<style>
.notion-report-container {

    font-family: -apple-system, BlinkMacSystemFont, 'System UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
    --font-xs: 0.6875rem;
    --font-sm: 0.75rem;
    --font-md: 0.8125rem;
    --font-lg: 0.875rem;
    --notion-bg: #fff;
    --notion-border: #e0e0e0;
    --notion-hover: #f5f5f5;
    --notion-active: #ebebeb;
    --notion-text: #37352f;
    --notion-text-light: #787774;
    --notion-blue: #337ea9;
    --notion-green: #2d9d78;
    --notion-purple: #9065b0;
    color: var(--notion-text);
    padding: 1rem;
}

.notion-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 1.5rem;
    gap: 1rem;
    flex-wrap: wrap;
}

.notion-header-left h3 {
    font-size: 1.25rem;
    font-weight: 600;
    margin: 0 0 0.5rem 0;
    color: var(--notion-text);
}

.notion-pills {
    display: flex;
    gap: 0.5rem;
}

.notion-pill {
    font-size: var(--font-xs);
    background: var(--notion-hover);
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    color: var(--notion-text-light);
}

.notion-header-right {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    flex-wrap: wrap;
}

.notion-search {
    position: relative;
    display: flex;
    align-items: center;
}

.notion-search i {
    position: absolute;
    left: 0.5rem;
    color: var(--notion-text-light);
    font-size: var(--font-md);
}

.notion-search input {
    padding: 0.375rem 0.75rem 0.375rem 1.75rem;
    border: 1px solid var(--notion-border);
    border-radius: 4px;
    font-size: var(--font-sm);
    min-width: 180px;
    transition: all 0.2s;
    height: 32px;
}

.notion-search input:focus {
    outline: none;
    border-color: var(--notion-blue);
    box-shadow: 0 0 0 2px rgba(51, 126, 169, 0.1);
}

.notion-tabs {
    display: flex;
    border: 1px solid var(--notion-border);
    border-radius: 4px;
    overflow: hidden;
    height: 32px;
}

.notion-tabs button {
    background: none;
    border: none;
    padding: 0 0.75rem;
    font-size: var(--font-sm);
    display: flex;
    align-items: center;
    gap: 0.25rem;
    cursor: pointer;
    color: var(--notion-text-light);
    transition: all 0.2s;
}

.notion-tabs button:hover {
    background: var(--notion-hover);
}

.notion-tabs button.active {
    background: var(--notion-active);
    color: var(--notion-text);
}

.notion-tabs button i {
    font-size: var(--font-md);
}

.notion-select {
    padding: 0 0.75rem;
    border: 1px solid var(--notion-border);
    border-radius: 4px;
    font-size: var(--font-sm);
    background-color: white;
    cursor: pointer;
    transition: all 0.2s;
    height: 32px;
}

.notion-select:focus {
    outline: none;
    border-color: var(--notion-blue);
    box-shadow: 0 0 0 2px rgba(51, 126, 169, 0.1);
}

.notion-summary-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 0.75rem;
    margin-bottom: 1.5rem;
}

.notion-summary-card {
    background: white;
    border: 1px solid var(--notion-border);
    border-radius: 6px;
    padding: 0.75rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    transition: transform 0.2s;
}

.notion-summary-card:hover {
    transform: translateY(-2px);
}

.card-icon {
    width: 32px;
    height: 32px;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    flex-shrink: 0;
}

.card-icon i {
    font-size: 1rem;
}

.bg-blue { background: var(--notion-blue); }
.bg-green { background: var(--notion-green); }
.bg-purple { background: var(--notion-purple); }

.filter-container {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 6px 0;
    overflow-x: auto;
    scrollbar-width: none; /* Hide scrollbar for Firefox */
    -ms-overflow-style: none; /* Hide scrollbar for IE/Edge */
}

.filter-container::-webkit-scrollbar {
    display: none; /* Hide scrollbar for Chrome/Safari */
}

.filter-search {
    position: relative;
    display: flex;
    align-items: center;
    min-width: 160px;
    height: 28px;
    background: #ffffff;
    border: 1px solid #e0e0e0;
    border-radius: 4px;
    padding: 0 8px;
    transition: all 0.2s ease;
}

.filter-search:hover {
    border-color: #b3b3b3;
}

.filter-search:focus-within {
    border-color: #337ea9;
    box-shadow: 0 0 0 2px rgba(51, 126, 169, 0.1);
}

.filter-icon {
    font-size: 11px;
    color: #787774;
    margin-right: 6px;
}

.filter-input {
    border: none;
    outline: none;
    font-size: 10px;
    width: 100%;
    height: 100%;
    padding: 0;
    background: transparent;
}

.filter-input::placeholder {
    color: #b3b3b3;
}

.filter-select {
    position: relative;
    min-width: 120px;
    height: 28px;
}

.filter-dropdown {
    width: 100%;
    height: 100%;
    padding: 0 24px 0 8px;
    border: 1px solid #e0e0e0;
    border-radius: 4px;
    font-size: 10px;
    appearance: none;
    background: #ffffff;
    cursor: pointer;
    transition: all 0.2s ease;
}

.filter-dropdown:hover {
    border-color: #b3b3b3;
}

.filter-dropdown:focus {
    border-color: #337ea9;
    box-shadow: 0 0 0 2px rgba(51, 126, 169, 0.1);
    outline: none;
}

.dropdown-arrow {
    position: absolute;
    right: 6px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 10px;
    color: #787774;
    pointer-events: none;
}

.filter-toggle-group {
    display: flex;
    height: 28px;
    border-radius: 4px;
    overflow: hidden;
    border: 1px solid #e0e0e0;
    background: #ffffff;
}

.filter-toggle {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
    padding: 0 10px;
    height: 100%;
    border: none;
    background: transparent;
    font-size: 10px;
    color: #787774;
    cursor: pointer;
    transition: all 0.2s ease;
}

.filter-toggle:hover {
    background: #f5f5f5;
}

.filter-toggle.active {
    background: #f0f7ff;
    color: #337ea9;
    font-weight: 500;
}

.filter-toggle i {
    font-size: 11px;
}

@media (max-width: 576px) {
    .filter-container {
        gap: 6px;
    }
    
    .filter-search {
        min-width: 140px;
    }
    
    .filter-select {
        min-width: 100px;
    }
    
    .filter-toggle {
        padding: 0 8px;
    }
}

.card-label {
    font-size: var(--font-xs);
    color: var(--notion-text-light);
    margin-bottom: 0.15rem;
}

.card-value {
    font-size: var(--font-lg);
    font-weight: 600;
    line-height: 1.2;
}

.card-subtext {
    font-size: var(--font-xs);
    color: var(--notion-text-light);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 120px;
}

.notion-chart-container {
    height: 300px;
    background: white;
    border: 1px solid var(--notion-border);
    border-radius: 6px;
    padding: 1rem;
    margin-bottom: 1.5rem;
}

.notion-table-container {
    background: white;
    border: 1px solid var(--notion-border);
    border-radius: 6px;
    width: 100%;
    max-width: 100%;
    overflow: hidden;
}

.notion-table {
    width: 100%;
    border-collapse: collapse;
    font-size: var(--font-sm);
    table-layout: auto;
}

.notion-table th {
    text-align: left;
    padding: 6px 10px;
    background: var(--notion-hover);
    color: var(--notion-text-light);
    font-weight: 500;
    position: sticky;
    top: 0;
    z-index: 10;
    border-bottom: 1px solid var(--notion-border);
    white-space: nowrap;
    font-size: 10px;
}

.notion-table td {
    padding: 6px 10px;
    border-bottom: 1px solid var(--notion-border);
    vertical-align: middle;
    white-space: nowrap;
    font-size: 9px;
}

.notion-table-row:hover td {
    background: var(--notion-hover);
    cursor: pointer;
}

.notion-total-row td {
    font-weight: 600;
    background: #f9f9f9;
}




@media (max-width: 768px) {
    .notion-header {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .notion-summary-cards {
        grid-template-columns: 1fr;
    }
    
    .notion-header-right {
        width: 100%;
    }
    
    .notion-search input {
        flex-grow: 1;
    }
}
</style>

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

            allRows.forEach(row => {
                const typeCell = row.children[0].textContent.toLowerCase();
                const categoryCell = row.children[1]?.textContent.toLowerCase() || '';
                const matchesSearch = typeCell.includes(search) || categoryCell.includes(search);
                const matchesType = type === 'all' || typeCell === type.toLowerCase();
                row.style.display = matchesSearch && matchesType ? '' : 'none';
            });
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


    