@extends('layouts.app')

@section('content')
<div class="card shadow-sm">
    <div class="card-header py-2 d-flex justify-content-between align-items-center">
        <h6 class="mb-0 text-muted">Expense Categories</h6>
        <a href="javascript:void(0)" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
            Add Category
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

<!-- Add Modal -->
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog">
      <form id="addCategoryForm" class="modal-content">
        <div class="modal-header">
          <h6 class="modal-title">Add Expense Category</h6>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-2">
            <label>Expense Type</label>
            <select name="expense_type_id" class="form-select form-select-sm" required>
              @foreach ($expenseTypes as $type)
                <option value="{{ $type->id }}">{{ $type->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="mb-2">
            <label>Category Name</label>
            <input type="text" name="name" class="form-control form-control-sm" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-sm btn-primary">Save</button>
        </div>
      </form>
    </div>
  </div>
  
  <!-- Edit Modal -->
  <div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
      <form id="editCategoryForm" class="modal-content">
        @csrf
        @method('PUT')
        <input type="hidden" name="id" id="edit_id">
        <div class="modal-header">
          <h6 class="modal-title">Edit Expense Category</h6>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-2">
            <label>Expense Type</label>
            <select name="expense_type_id" id="edit_expense_type_id" class="form-select form-select-sm" required>
              @foreach ($expenseTypes as $type)
                <option value="{{ $type->id }}">{{ $type->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="mb-2">
            <label>Category Name</label>
            <input type="text" name="name" id="edit_name" class="form-control form-control-sm" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-sm btn-primary">Update</button>
        </div>
      </form>
    </div>
  </div>
  
@endsection

@push('scripts')
<script>
    $(document).ready(function () {
      if ($.fn.DataTable.isDataTable('#categoryTable')) {
            $('#categoryTable').DataTable().destroy();
        }
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

    // Setup CSRF Token untuk semua request AJAX
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Submit form tambah
    $('#addCategoryForm').submit(function (e) {
        e.preventDefault();
        $.ajax({
            url: "{{ route('admin.expense-category.store') }}",
            method: "POST",
            data: $(this).serialize(),
            success: function (res) {
                $('#addModal').modal('hide');
                $('#categoryTable').DataTable().ajax.reload();
                $('#addCategoryForm')[0].reset();
                Swal.fire('Sukses', res.message, 'success');
            },
            error: function (err) {
                let errors = err.responseJSON?.errors;
                Swal.fire('Gagal', Object.values(errors).flat().join('\n') || 'Gagal menambahkan data', 'error');
            }
        });
    });

    
   // Handle Edit (show modal dan isi data)
    $(document).on('click', '.btn-edit', function () {
        let id = $(this).data('id');

        // Gunakan url langsung untuk menghindari error route
        $.get("{{ url('admin/master-data/expense-category') }}/" + id, function (data) {
            $('#edit_id').val(data.id);
            $('#edit_name').val(data.name);
            $('#edit_expense_type_id').val(data.expense_type_id);
            $('#editModal').modal('show');
        }).fail(function (xhr) {
            let message = xhr.responseJSON?.message || 'Gagal mengambil data';
            Swal.fire('Error', message, 'error');
        });
    });

    // Submit edit form via AJAX
    $('#editCategoryForm').submit(function (e) {
        e.preventDefault();
        const id = $('#edit_id').val();

        $.ajax({
            url: "{{ url('admin/master-data/expense-category') }}/" + id,
            method: "POST",
            data: $(this).serialize(),
            success: function (res) {
                $('#editModal').modal('hide');
                $('#categoryTable').DataTable().ajax.reload();
                Swal.fire('Sukses', res.message, 'success');
            },
            error: function (err) {
                let errors = err.responseJSON?.errors;
                Swal.fire('Gagal', Object.values(errors).flat().join('\n') || 'Gagal update data', 'error');
            }
        });
    });

    // Handle Delete
    $(document).on('click', '.btn-delete', function () {
        let id = $(this).data('id');

        Swal.fire({
            title: 'Yakin ingin menghapus?',
            text: "Data tidak bisa dikembalikan setelah dihapus!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/admin/master-data/expense-category/${id}`, // langsung hardcode URL
                    method: "POST", // Gunakan POST
                    data: {
                        _token: '{{ csrf_token() }}',
                        _method: 'DELETE' // Spoof DELETE method
                    },
                    success: function (res) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: res.message,
                            timer: 1500,
                            showConfirmButton: false
                        });
                        $('#categoryTable').DataTable().ajax.reload(); // <--- perbaikan ID table
                    },
                    error: function (xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: xhr.responseJSON?.message || 'Gagal menghapus data'
                        });
                    }
                });
            }
        });
    });

</script>
@endpush
