@extends('layouts.app')

<style>
    #itemsTable th, #itemsTable td {
        vertical-align: middle;
    }
</style>

@section('content')
<div class="card small"> {{-- tambahkan class small di sini --}}
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="mb-0">All Items</h6> {{-- gunakan h6 untuk lebih kecil --}}
    </div>
    <div class="card-body p-3"> {{-- padding bisa disesuaikan --}}
        <table class="table table-bordered table-hover table-sm text-sm" id="itemsTable"> {{-- gunakan table-sm dan text-sm --}}
            <thead class="table-light">
                <tr>
                    <th>No</th>
                    <th>Created At</th>
                    <th>Unique</th>
                    <th>Description</th>
                    <th>Qty</th>
                    <th>Nominal</th>
                    <th>Total</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
@endsection


@push('scripts')
<script>
    $(function () {
        $('#itemsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.items.index') }}",
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'created_at', name: 'created_at' },
                { data: 'code_settlement', name: 'code_settlement' },
                { data: 'description', name: 'description' },
                { data: 'qty', name: 'qty' },
                { data: 'nominal', name: 'nominal' },
                { data: 'total', name: 'total' },
            ]
        });
    });
</script>
@endpush
