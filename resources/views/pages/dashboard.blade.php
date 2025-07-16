@extends('layouts.app')

@section('content')
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center py-3 px-4">
        <h6 class="mb-0 fw-semibold text-muted text-uppercase" style="font-size: 13px;">All Report</h6>
    </div>
    <div class="card-body px-0 pt-2 pb-3">
        <div class="table-responsive px-3">
            <table class="table table-hover table-sm align-middle notion-table" id="allReportTable">
                <thead class="table-light text-secondary">
                    <tr>
                        <th>No</th>
                        <th>Advance Date</th> 
                        <th>Settlement Date</th>
                        <th class="text-nowrap">Advance Code</th>
                        <th class="text-nowrap">Settlement Code</th>
                        <th>Expense Type</th>
                        <th>Expense Category</th>
                        <th>Vendor Name</th>
                        <th>Description</th>
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
            ajax: "{{ route('admin.all-report') }}",
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', className: 'text-sm', orderable: false, searchable: false },
                { data: 'date_advance', name: 'date_advance', className: 'text-sm' },
                { data: 'date_settlement', name: 'date_settlement', className: 'text-sm' },
                { data: 'code_advance', name: 'code_advance', className: 'text-sm text-nowrap' },
                { data: 'code_settlement', name: 'code_settlement', className: 'text-sm text-nowrap', defaultContent: '-' },
                { data: 'expense_type', name: 'expense_type', className: 'text-sm', defaultContent: '-' },
                { data: 'expense_category', name: 'expense_category', className: 'text-sm', defaultContent: '-' },
                { data: 'vendor_name', name: 'vendor_name', className: 'text-sm', defaultContent: '-' },
                { data: 'description', name: 'description', className: 'text-sm' },
                { data: 'nominal_advance', name: 'nominal_advance', className: 'text-sm text-end' },
                {
                    data: 'id',
                    name: 'action',
                    orderable: false, 
                    searchable: false,
                    className: 'text-sm text-center',
                    render: function(data, type, row) {
                        const baseUrl = "{{ url('admin/all-report/settlement') }}";
                        return `
                            <a href="${baseUrl}/${data}" class="btn btn-success btn-sm">
                                Detail
                            </a>
                        `;
                    }
                }
            ]
        });
    });
</script>
@endpush
