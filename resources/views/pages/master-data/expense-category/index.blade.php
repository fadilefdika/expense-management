@extends('layouts.app')

@section('content')
<div class="card shadow-sm">
    <div class="card-header py-2 d-flex justify-content-between align-items-center">
        <h6 class="mb-0 text-muted">Expense Categories</h6>
        <a href="{{ route('admin.expense-category.store') }}" class="btn btn-sm btn-primary">
            <i class="bi bi-plus-circle me-1"></i> Add Category
        </a>
    </div>
    <div class="card-body p-2">
        <table id="categoryTable" class="table table-bordered table-hover table-sm text-sm">
            <thead class="table-light small">
                <tr>
                    <th style="width: 50px;">No</th>
                    <th>Expense Type</th>
                    <th>Name</th>
                    <th style="width: 120px;">Action</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        $('#categoryTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.expense-category.index') }}",
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'expense_type', name: 'expense_type' },
                { data: 'name', name: 'name' },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false,
                    className: 'text-center'
                },
            ],
        });
    });
</script>
@endpush
