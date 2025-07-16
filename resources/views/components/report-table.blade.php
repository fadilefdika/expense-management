@props([
    'headers' => [],
    'rows' => [],
    'monthlyTotals' => [],
    'title' => 'Report Table',
    'label1' => 'Expense Type',
    'label2' => 'Expense Category',
])

<div class="card shadow-sm rounded-4 border-0 mb-4">
    <div class="card-header bg-white border-bottom py-3 px-4 d-flex justify-content-between align-items-center">
        <h6 class="mb-0 text-secondary fw-semibold" style="font-size: 15px;">{{ $title }}</h6>
    </div>

    <div class="card-body p-0">
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
                        <tr class="table-row-hover">
                            <td class="sticky-col start-0 bg-white text-uppercase custom-text-md">{{ $row['expense_type'] ?? $row['vendor'] ?? '-' }}</td>
                            @if(isset($row['category']))
                                <td class="sticky-col start-1 bg-white text-uppercase custom-text-md">{{ $row['category'] }}</td>
                            @endif
                            @foreach($row['monthly'] as $value)
                                <td class="text-end">{{ number_format($value, 0, ',', '.') }}</td>
                            @endforeach
                            <td class="text-end fw-semibold">{{ number_format($row['total'], 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-white text-dark fw-semibold text-end border-top">
                    <tr>
                        <td class="sticky-col start-0 bg-white text-start">Grand Total</td>
                        @if(isset($rows[0]['category']))
                            <td class="sticky-col start-1 bg-white"></td>
                        @endif
                        @foreach($monthlyTotals as $total)
                            <td>{{ number_format($total, 0, ',', '.') }}</td>
                        @endforeach
                        <td class="bg-light text-dark">{{ number_format(array_sum($monthlyTotals), 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        {{-- MOBILE VIEW (ACCORDION) --}}
        <div class="mobile-summary px-3 py-2">
            @foreach($monthlyTotals as $index => $total)
                <div class="mb-3 accordion-card px-3 py-2">
                    <div x-data="{ open: false }">
                        <button class="w-100 d-flex justify-content-between align-items-center" @click="open = !open">
                            <span class="fw-semibold">{{ $headers[$index-1] ?? 'Unknown' }}</span>
                            <div class="d-flex align-items-center gap-2">
                                <span>{{ number_format($total, 0, ',', '.') }}</span>
                                <svg :class="{'rotate-180': open}" class="transition-transform duration-300" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M6 9l6 6 6-6" />
                                </svg>
                            </div>
                        </button>
        
                        <div x-show="open" x-transition.duration.200ms class="accordion-content">
                            @foreach($rows as $row)
                                @if(isset($row['monthly'][$index]) && $row['monthly'][$index] > 0)
                                    <div class="d-flex justify-content-between small mb-1">
                                        <span class="text-muted">
                                            {{ $row['expense_type'] ?? $row['vendor'] }}
                                            @if(isset($row['category']))
                                                - {{ $row['category'] }}
                                            @endif
                                        </span>
                                        <span>{{ number_format($row['monthly'][$index], 0, ',', '.') }}</span>
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
        --primary-color: #1fbf59;
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
        font-size: 0.72rem;
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

    /* Mobile Accordion Styling */
    .mobile-summary .accordion-card {
        background-color: #fff;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        transition: all 0.3s ease;
    }

    .mobile-summary .accordion-card:hover {
        background-color: var(--primary-light);
    }

    .mobile-summary button {
        background: none;
        border: none;
        padding: 0.6rem 0;
        font-size: 0.85rem;
        font-weight: 600;
        color: #343a40;
    }

    .mobile-summary button:hover {
        color: var(--primary-color);
    }

    .mobile-summary .accordion-content {
        background-color: #f9fdfb;
        padding: 0.6rem 0.4rem 0.2rem;
        border-top: 1px solid #e1e1e1;
        font-size: 0.75rem;
    }

    .mobile-summary .accordion-content .text-muted {
        color: #6c757d;
    }

    .rotate-180 {
        transform: rotate(180deg);
    }

    .transition-transform {
        transition: transform 0.3s ease;
    }

</style>
@endpush


