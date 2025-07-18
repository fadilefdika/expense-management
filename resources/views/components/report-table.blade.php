@props([
    'headers' => [],
    'rows' => [],
    'monthlyTotals' => [],
    'expenseTypes' => [],
    'title' => 'Report Table',
    'label1' => 'Expense Type',
    'label2' => 'Expense Category',
    'idPrefix' => 'report', // default
])

<div class="card shadow-sm rounded-4 border-0 mb-4">
    <div class="card-header bg-white border-bottom py-3 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h6 class="mb-0 text-secondary fw-semibold" style="font-size: 15px;">
            {{ $title }}
        </h6>
    
        @if(count($expenseTypes))
        <div class="d-flex align-items-center gap-2">
            <label for="filter-expense" class="form-label mb-0 small text-nowrap">Expense Type:</label>
            <select id="{{ $idPrefix }}-filter-expense" class="form-select form-select-sm" style="min-width: 150px;">
                <option value="all">All</option>
                @foreach($expenseTypes as $type)
                    <option value="{{ $type }}">{{ $type }}</option>
                @endforeach
            </select>
        </div>
        @endif
    </div>    

    <div class="card-body px-1">

        {{-- DESKTOP TABLE --}}
        <div class="table-responsive custom-scroll desktop-table">
            <table class="table table-hover table-sm table-borderless text-nowrap align-middle mb-0" style="min-width: 100%;">
                <thead class="bg-light text-dark sticky-top shadow-sm" style="top: 0; z-index: 5;">
                    <tr>
                        <th class="sticky-col start-0 bg-white">{{ $label1 }}</th>
                        @if(isset($rows[0]['category']))
                            <th class="sticky-col start-1 bg-white">{{ $label2 }}</th>
                        @endif
                        @foreach($headers as $header)
                            <th class="text-center">{{ $header }}</th>
                        @endforeach
                        <th class="bg-white text-center">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rows as $row)
                    <tr class="table-row-hover report-table-row" data-type="{{ $row['expense_type'] ?? $row['vendor'] ?? '-' }}" data-prefix="{{ $idPrefix }}">
                            <td class="sticky-col start-0 bg-white text-uppercase custom-text-md">{{ $row['expense_type'] ?? $row['vendor'] ?? '-' }}</td>
                            @if(isset($row['category']))
                                <td class="sticky-col start-1 bg-white text-uppercase custom-text-md">{{ $row['category'] }}</td>
                            @endif
                            @foreach($row['monthly'] as $value)
                                <td class="text-end monthly-value">{{ number_format($value, 0, ',', '.') }}</td>
                            @endforeach
                            <td class="text-end fw-semibold">{{ number_format($row['total'], 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
                {{-- @php
                    dd($monthlyTotals);   
                @endphp --}}
                <tfoot class="bg-white text-dark fw-semibold text-end border-top" id="{{ $idPrefix }}-grand-total-footer">
                    <tr>
                        <td class="sticky-col start-0 bg-white text-start">Grand Total</td>
                        @if(isset($rows[0]['category']))
                            <td class="sticky-col start-1 bg-white"></td>
                        @endif
                        @foreach($monthlyTotals as $i => $total)
                            <td class="monthly-total" data-index="{{ $i }}" id="{{ $idPrefix }}-monthly-total-{{ $i }}">{{ number_format($total, 0, ',', '.') }}</td>
                        @endforeach
                        <td id="{{ $idPrefix }}-grand-total">{{ number_format(array_sum($monthlyTotals), 0, ',', '.') }}</td>
                    </tr>
                </tfoot>                
            </table>
        </div>

        {{-- MOBILE VIEW (ACCORDION) --}}
        <div class="mobile-summary px-3 py-2">
            @foreach($monthlyTotals as $index => $total)
                <div class="mb-3 accordion-card shadow-sm">
                    <div x-data="{ open: false }">
                        <button
                            class="accordion-toggle w-100 d-flex justify-content-between align-items-center px-3 py-2"
                            @click="open = !open"
                        >
                            <span class="fw-semibold text-dark">{{ $headers[$index - 1] ?? 'Unknown' }}</span>
                            <div class="d-flex align-items-center gap-2">
                                <span class="text-dark">{{ number_format($total, 0, ',', '.') }}</span>
                                <svg
                                    :class="{'rotate-180': open}"
                                    class="transition-transform duration-300"
                                    width="16"
                                    height="16"
                                    fill="none"
                                    stroke="#1fbf59"
                                    stroke-width="2"
                                    viewBox="0 0 24 24"
                                >
                                    <path d="M6 9l6 6 6-6" />
                                </svg>
                            </div>
                        </button>
        
                        <div x-show="open" x-transition.duration.300ms class="accordion-content">
                            @foreach($rows as $row)
                                @if(isset($row['monthly'][$index]) && $row['monthly'][$index] > 0)
                                    <div class="d-flex justify-content-between mb-2 mt-1 px-3 small" style="font-size: 9px">
                                        <span class="text-muted">
                                            {{ $row['expense_type'] ?? $row['vendor'] }}
                                            @if(isset($row['category']))
                                                - {{ $row['category'] }}
                                            @endif
                                        </span>
                                        <span class="fw-semibold text-success">{{ number_format($row['monthly'][$index], 0, ',', '.') }}</span>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        </div>        
        
    </div>
</div>


@push('styles')
<style>
    :root {
        --primary-light: #e6f8ec;
    }

    /* Sticky Columns */
    .sticky-col {
        position: sticky;
        z-index: 3;
        background: #fff;
    }

    .start-0 {
        left: 0;
        min-width: 180px;
        max-width: 180px;
    }

    .start-1 {
        left: 180px;
        min-width: 160px;
        max-width: 160px;
    }

    /* Scroll Wrapper */
    .custom-scroll {
        max-height: 70vh;
        overflow: auto;
        -webkit-overflow-scrolling: touch;
        scroll-behavior: smooth;
    }

    .custom-scroll::-webkit-scrollbar {
        height: 6px;
        width: 6px;
    }

    .custom-scroll::-webkit-scrollbar-thumb {
        background: #bbb;
        border-radius: 6px;
    }

    /* Table Style */
    table th,
    table td {
        padding: 0.55rem 0.75rem;
        font-size: 0.78rem;
        white-space: nowrap;
        vertical-align: middle;
    }

    table thead {
        background-color: var(--primary-light);
        color: #2d2f32;
    }

    .custom-text-md {
        font-size: 10px;
        font-weight: 600;
        color: #343a40;
    }

    .table-row-hover:hover {
        background-color: #f3fef8;
        transition: background-color 0.25s ease-in-out;
    }

    /* Responsive Toggle */
    @media (max-width: 768px) {
        .start-0 {
            min-width: 140px;
            max-width: 140px;
        }

        .start-1 {
            left: 140px;
            min-width: 120px;
            max-width: 120px;
        }

        .desktop-table {
            display: none !important;
        }

        .mobile-summary {
            display: block !important;
        }
    }

    @media (min-width: 769px) {
        .desktop-table {
            display: block !important;
        }

        .mobile-summary {
            display: none !important;
        }
    }

    .mobile-summary .accordion-card {
        background-color: #fff;
        border: 1px solid #e0e0e0;
        border-radius: 6px;
        overflow: hidden;
        transition: box-shadow 0.2s ease;
    }

    .mobile-summary .accordion-card:hover {
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
    }

    .mobile-summary button {
        background: none;
        border: none;
        font-size: 0.85rem;
        font-weight: 500;
        color: #212529;
    }

    .accordion-content {
        color: #495057;
        border-top: 1px solid #dee2e6;
    }

    .rotate-180 {
        transform: rotate(180deg);
    }

    .transition-transform {
        transition: transform 0.3s ease;
    }

</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const select = document.getElementById('filter-expense');
        if (select) {
            select.addEventListener('change', function () {
                const selectedType = this.value.toLowerCase();
                const rows = document.querySelectorAll('.report-table-row');

                rows.forEach(row => {
                    const rowType = row.dataset.type?.toLowerCase() || '';
                    if (!selectedType || rowType === selectedType) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        }
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const prefix = @json($idPrefix);
        const filter = document.getElementById(`${prefix}-filter-expense`);
        const tableSelector = `.report-table-row[data-prefix="${prefix}"]`;

        function updateGrandTotal() {
            const visibleRows = document.querySelectorAll(`${tableSelector}:not([style*="display: none"])`);
            const monthlyCount = document.querySelectorAll(`#${prefix}-grand-total-footer .monthly-total`).length;
            const totals = new Array(monthlyCount).fill(0);

            visibleRows.forEach(row => {
                const monthlyCells = row.querySelectorAll('.monthly-value');
                monthlyCells.forEach((cell, i) => {
                    const val = parseInt(cell.textContent.replaceAll('.', '').trim()) || 0;
                    totals[i] += val;
                });
            });

            // Update monthly total
            totals.forEach((total, i) => {
                const cell = document.getElementById(`${prefix}-monthly-total-${i}`);
                if (cell) cell.textContent = total.toLocaleString('id-ID');
            });

            // Update grand total
            const totalCell = document.getElementById(`${prefix}-grand-total`);
            if (totalCell) totalCell.textContent = totals.reduce((a, b) => a + b, 0).toLocaleString('id-ID');
        }

        function filterTable() {
            const selectedType = filter.value.toLowerCase();
            const rows = document.querySelectorAll(tableSelector);
            rows.forEach(row => {
                const type = row.getAttribute('data-type')?.toLowerCase() || '';
                row.style.display = (selectedType === 'all' || type === selectedType) ? '' : 'none';
            });
            updateGrandTotal();
        }

        if (filter) {
            filter.addEventListener('change', filterTable);
        }

        updateGrandTotal();
    });
</script>
@endpush
