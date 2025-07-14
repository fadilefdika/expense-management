@extends('layouts.app')

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">ALL Data</h5>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-hover" id="advanceTable">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal Advance</th>
			            <th>Tanggal Settlement</th>
                        <th>Kode Advance</th>
			            <th>Kode Settlement</th>
			            <th>Expense Type</th>
			            <th>Expense Category</th>
			            <th>Vendor Name</th>
                        <th>Description</th>
                        <th>Nominal(Rp)</th>
                        <th>Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    $(function () {
        $('#advanceTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.all-report') }}",
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', className: 'text-sm', orderable: false, searchable: false },
                { data: 'date_advance', name: 'date_advance', className: 'text-sm' },
                { data: 'date_settlement', name: 'date_settlement', className: 'text-sm' },
                { data: 'code_advance', name: 'code_advance', className: 'text-sm' },
                { data: 'code_settlement', name: 'code_settlement', className: 'text-sm', defaultContent: '-' }, // opsional jika belum ada
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
                            <a href="${baseUrl}/${data}" class="btn btn-sm btn-success">
                                Settlement
                            </a>
                        `;
                    }
                }
            ]
        });
    });

    $(document).on('click', '.btn-settle', function() {
        const id = $(this).data('id');
        console.log('Buat settlement untuk ID:', id);
        
        window.location.href = `/admin/settlement/${id}`;
    });

</script>
@endpush

