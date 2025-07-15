@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Expense Report</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive" style="max-height: 80vh; overflow: auto;">
                <table class="table table-bordered table-sm table-hover table-striped mb-0 text-nowrap" style="font-size: 0.82rem; min-width: 100%;">
                    <thead class="bg-dark text-white text-center sticky-top" style="top: 0; z-index: 10;">
                        <tr>
                            <th class="sticky-col bg-dark text-white start-0">Expense Type</th>
                            <th class="sticky-col bg-dark text-white start-1">Expense Category</th>
                            @for ($i = 1; $i <= 12; $i++)
                                <th>{{ DateTime::createFromFormat('!m', $i)->format('M') }}</th>
                            @endfor
                            <th class="bg-secondary text-white">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($report as $row)
                            <tr>
                                <td class="sticky-col bg-light start-0 fw-normal text-uppercase small">{{ $row['expense_type'] }}</td>
                                <td class="sticky-col bg-light start-1 text-uppercase small">{{ $row['category'] }}</td>                                
                                @for ($i = 1; $i <= 12; $i++)
                                    <td class="text-end">{{ number_format($row['monthly'][$i], 0, ',', '.') }}</td>
                                @endfor
                                <td class="text-end bg-light fw-bold">{{ number_format($row['total'], 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-light text-dark fw-semibold text-end sticky-footer border-top">
                        <tr>
                            <td class="sticky-col start-0 bg-light text-start">Grand Total</td>
                            <td class="sticky-col start-1 bg-light"></td>
                            @for ($i = 1; $i <= 12; $i++)
                                <td>{{ number_format($monthlyTotals[$i], 0, ',', '.') }}</td>
                            @endfor
                            <td class="bg-secondary text-white">{{ number_format(array_sum($monthlyTotals), 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>                                       
                </table>
            </div>
        </div>
    </div>
</div>
<style>
    .sticky-col {
        position: sticky;
        z-index: 2;
        background: inherit;
    }

    .start-0 {
        left: 0;
    }

    .start-1 {
        left: 120px; /* sesuaikan jika kolom terlalu sempit */
    }

    @media (max-width: 768px) {
        table {
            font-size: 0.65rem;
        }
    }

    .table-responsive {
        max-height: 80vh;
        overflow: auto;
        -webkit-overflow-scrolling: touch;
        scroll-behavior: smooth;
    }

    .table-responsive::-webkit-scrollbar {
        height: 6px;
    }

    .table-responsive::-webkit-scrollbar-thumb {
        background: #aaa;
        border-radius: 10px;
    }

    th, td {
        white-space: nowrap;
        vertical-align: middle;
        font-size: 0.68rem;
        padding: 0.3rem 0.4rem;
        line-height: 1.1;
    }

    .bg-secondary {
        background-color: #6c757d !important;
    }

    .thead-dark th {
        background-color: #343a40;
        color: #fff;
    }
</style>


@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const container = document.querySelector('.table-responsive');

        // Hanya aktifkan horizontal scroll dengan mousewheel untuk layar kecil (misal < 768px)
        if (window.innerWidth < 768) {
            container.addEventListener('wheel', function (e) {
                const isScrollableX = this.scrollWidth > this.clientWidth;

                if (isScrollableX && Math.abs(e.deltaY) > Math.abs(e.deltaX)) {
                    this.scrollLeft += e.deltaY;
                    e.preventDefault();
                }
            }, { passive: false });
        }
    });
</script>


@endpush

