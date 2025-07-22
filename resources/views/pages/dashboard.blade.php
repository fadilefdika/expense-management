@extends('layouts.app')

@section('content')
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center py-3 px-4">
        <h6 class="mb-0 fw-semibold text-muted text-uppercase" style="font-size: 13px;">All Report</h6>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.all-report.create') }}" class="btn btn-primary btn-sm">
                Add Expense
            </a>
            {{-- <a href="{{ route('admin.advance.export-excel') }}" class="btn btn-outline-success btn-sm">
                Export
            </a> --}}
        </div>
    </div>
    <div class="card-body px-0 pt-2 pb-3">
        <div class="table-responsive px-3">
            <table class="table table-hover table-sm align-middle notion-table" id="allReportTable">
                <thead class="table-light text-secondary">
                    <tr>
                        <th>No</th>
                        <th>Expense Date</th> 
                        <th>Settlement Date</th>
                        <th class="text-nowrap">Expense Code</th>
                        <th class="text-nowrap">Settlement Code</th>
                        <th>Expense Type</th>
                        <th>Expense Category</th>
                        <th>Vendor Name</th>
                        <th>Description</th>
                        <th class="d-none">Updated At</th>
                        <th class="text-end">Amount (Rp)</th>
                        <th class="text-center">Action</th>
                    </tr>                    
                </thead>
            </table>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>

    .card {
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
        border: 1px solid #e6e6e6;
    }

    .card-header {
        background-color: transparent;
        border-bottom: 1px solid #eee;
    }

    .notion-table th,
    .notion-table td {
        font-size: 10px;
        vertical-align: middle;
        padding: 0.65rem 0.75rem;
        line-height: 1.4;
    }

    .notion-table thead th {
        background-color: #f9fafb;
        font-weight: 600;
        border-bottom: 1px solid #e5e7eb;
    }

    .notion-table tbody tr:hover {
        background-color: #f8f9fc;
    }

    .notion-table td:nth-child(4),
    .notion-table td:nth-child(5) {
        white-space: nowrap;
    }

    .notion-table td:nth-child(9), /* Description column */
    .notion-table th:nth-child(9) {
        max-width: 400px;              /* diperbesar */
        min-width: 60px;
        white-space: normal !important;
        word-break: break-word;
        line-height: 1.5;
    }


    .notion-table td:nth-child(9) { /* Description */
        max-width: 280px;
        white-space: normal !important;
        word-break: break-word;
        line-height: 1.5;
    }

    .btn-sm {
        font-size: 10px;
        padding: 4px 10px;
        border-radius: 6px;
    }

    .btn-success {
        background-color: #22c55e;
        border-color: #22c55e;
    }

    .btn-success:hover {
        background-color: #16a34a;
        border-color: #16a34a;
    }

    /* DATATABLE */
    .dataTables_length label {
        font-size: 11px;
    }

    /* Search input */
    .dataTables_filter label {
        font-size: 11px;
    }

    /* Info text: 'Showing 1 to 10 of 100 entries' */
    .dataTables_info {
        font-size: 11px;
    }

    /* Pagination buttons */
    .dataTables_paginate {
        font-size: 11px;
    }

    .dataTables_paginate .paginate_button {
        font-size: 11px;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button {
        font-size: 12px !important; /* naikkan dari 10px */
        min-width: auto !important;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button.page-item .page-link,
    .dataTables_wrapper .dataTables_paginate .paginate_button .page-link {
        font-size: 12px !important;
        padding: 4px 10px !important;
        min-width: auto !important;
        line-height: 1.2 !important;
    }
</style>
@endpush

@push('scripts')
<script>
    $(function () {
        if ($.fn.DataTable.isDataTable('#allReportTable')) {
            $('#allReportTable').DataTable().destroy();
        }

        $('#allReportTable').DataTable({
            processing: true,
            serverSide: true,
            scrollX: true,
            order: [[9, 'desc']], // Kolom ke-3 = date_advance
            ajax: "{{ route('admin.all-report') }}",
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'date_advance', name: 'a.date_advance', className: 'text-sm' },
                { data: 'date_settlement', name: 'a.date_settlement', className: 'text-sm' },
                { data: 'code_advance', name: 'a.code_advance', className: 'text-sm text-nowrap' },
                { data: 'code_settlement', name: 'a.code_settlement', className: 'text-sm text-nowrap' },
                { data: 'expense_type', name: 'et.name', className: 'text-sm' },
                { data: 'expense_category', name: 'ec.name', className: 'text-sm' },
                { data: 'vendor_name', name: 'ev.name', className: 'text-sm' },
                { data: 'description', name: 'a.description', className: 'text-sm' },
                { data: 'updated_at', name: 'a.updated_at', visible: false },
                { data: 'nominal_advance', name: 'a.nominal_advance', className: 'text-sm text-end' },
                {
                    data: 'id',
                    name: 'a.id',
                    orderable: false,
                    searchable: false,
                    className: 'text-sm text-center',
                    render: function(data, type, row) {
                        const baseUrl = "{{ url('admin/all-report/settlement') }}";
                        if (row.date_settlement === '-' || row.date_settlement === null) {
                            return `<a href="${baseUrl}/${data}/edit" class="btn btn-success btn-sm">Detail</a>`;
                        } else {
                            return `<a href="${baseUrl}/${data}" class="btn btn-success btn-sm">Detail</a>`;
                        }
                    }
                }
            ]
        });
    });
</script>

    
@endpush
