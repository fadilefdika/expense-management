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
        <div class="table-responsive custom-scroll">
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
    </div>
</div>


@push('styles')
<style>
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


    .custom-scroll {
        max-height: 70vh;
        overflow: auto;
        -webkit-overflow-scrolling: touch;
        scroll-behavior: smooth;
    }

    .custom-scroll::-webkit-scrollbar {
        height: 6px;
    }

    .custom-scroll::-webkit-scrollbar-thumb {
        background: #ccc;
        border-radius: 6px;
    }

    table th, table td {
        padding: 0.5rem 0.6rem;
        font-size: 0.78rem;
        white-space: nowrap;
        vertical-align: middle;
    }

    .custom-text-md {
        font-size: 0.65rem; /* contoh ukuran custom */
        font-weight: 600;
    }

    .table-row-hover:hover {
        background-color: #f1f3f5;
        transition: background-color 0.2s ease-in-out;
    }

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
    }

</style>
@endpush
