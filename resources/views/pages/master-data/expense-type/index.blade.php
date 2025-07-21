@extends('layouts.app')

@section('content')
<div class="card shadow-sm">
    <div class="card-header py-2 d-flex justify-content-between align-items-center">
        <h6 class="mb-0 text-muted">Expense Type</h6>
        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
            Add Type
        </button>
    </div>
    <div class="card-body p-2">
        <table id="typeTable" class="table table-bordered table-hover table-sm text-sm w-100">
            <thead class="table-light small">
                <tr>
                    <th style="width: 50px;">No</th>
                    <th>Name</th>
                    <th style="width: 120px;">Action</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-sm modal-dialog-centered">
    <div class="modal-content">
      <form id="addTypeForm">
        <div class="modal-header">
          <h6 class="modal-title">Add Expense Type</h6>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div class="mb-2">
                <label class="form-label form-label-sm">Name<span class="text-danger"> *</span></label>
                <input type="text" name="name" class="form-control form-control-sm" required>
            </div>
        </div>
        <div class="modal-footer py-1">
          <button type="submit" class="btn btn-sm btn-primary">Save</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-sm modal-dialog-centered">
    <div class="modal-content">
      <form id="editTypeForm">
        <input type="hidden" name="id" id="edit_id">
        <div class="modal-header">
          <h6 class="modal-title">Edit Expense Type</h6>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div class="mb-2">
                <label class="form-label form-label-sm">Name<span class="text-danger"> *</span></label>
                <input type="text" name="name" id="edit_name" class="form-control form-control-sm" required>
            </div>
        </div>
        <div class="modal-footer py-1">
          <button type="submit" class="btn btn-sm btn-success">Update</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection


@push('scripts')
<script>
    const table = $('#typeTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('admin.expense-type.index') }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
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

    // Setup CSRF Token untuk semua request AJAX
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Submit form tambah
    $('#addTypeForm').submit(function(e){
        e.preventDefault();
        $.ajax({
            url: "{{ route('admin.expense-type.store') }}",
            method: "POST",
            data: $(this).serialize(),
            success: function(res){
                $('#addModal').modal('hide');
                $('#typeTable').DataTable().ajax.reload();
                $('#addTypeForm')[0].reset();
            },
            error: function(err) {
                console.error(err);
                let errors = err.responseJSON?.errors;

                if (errors) {
                    let messages = Object.values(errors).flat().join('<br>');
                    Swal.fire({
                        icon: 'error',
                        title: 'Validasi Gagal',
                        html: messages
                    });
                } else {
                    let message = err.responseJSON?.message || 'Gagal menambahkan data.';
                    Swal.fire({
                        icon: 'error',
                        title: 'Terjadi Kesalahan',
                        text: message
                    });
                }
            }
        });
    });

    // Klik tombol edit
    $(document).on('click', '.btn-edit', function () {
        let id = $(this).data('id');
        $.get("{{ route('admin.expense-type.show', ':id') }}".replace(':id', id), function (data) {
            $('#edit_id').val(data.id);
            $('#edit_name').val(data.name);
            $('#editModal').modal('show');
        }).fail(function(xhr){
            console.error(xhr);
            alert('Gagal mengambil data. Coba lagi.');
        });
    });

    // Submit form edit
    $('#editTypeForm').submit(function(e){
        e.preventDefault();
        const id = $('#edit_id').val();
        $.ajax({
            url: "{{ url('admin/master-data/expense-type') }}/" + id,
            method: "PUT",
            data: $(this).serialize(),
            success: function(res){
                $('#editModal').modal('hide');
                $('#typeTable').DataTable().ajax.reload();
            },
            error: function(err){
                console.error(err);
                let errors = err.responseJSON?.errors;
                if (errors) {
                    let messages = Object.values(errors).flat().join('\n');
                    alert(messages);
                } else {
                    alert('Gagal mengubah data.');
                }
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
                    url: "{{ route('admin.expense-type.destroy', ':id') }}".replace(':id', id),
                    method: "DELETE",
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (res) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: res.message,
                            timer: 1500,
                            showConfirmButton: false
                        });
                        $('#typeTable').DataTable().ajax.reload();
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
