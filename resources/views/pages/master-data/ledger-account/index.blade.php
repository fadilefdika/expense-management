@extends('layouts.app')

@section('content')
<div class="card shadow-sm">
    <div class="card-header py-2 d-flex justify-content-between align-items-center">
        <h6 class="mb-0 text-muted">GL Account</h6>
        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
            Add GL Account
        </button>
    </div>
    <div class="card-body p-2">
        <table id="ledgerAccountTable" class="table table-bordered table-hover table-sm text-sm w-100">
            <thead class="table-light small">
                <tr>
                    <th style="width: 50px;">No</th>
                    <th>GL Account</th>
                    <th>Desc COA</th>
                    <th>Tax Percent</th>
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
          <h6 class="modal-title">Add Type</h6>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div class="mb-2">
                <label class="form-label form-label-sm">GL Account<span class="text-danger"> *</span></label>
                <input type="text" name="ledger_account" class="form-control form-control-sm" required>
            </div>
            <div class="mb-2">
                <label class="form-label form-label-sm">Desc COA<span class="text-danger"> *</span></label>
                <input type="text" name="desc_coa" class="form-control form-control-sm" required>
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
          <h6 class="modal-title">Edit Type</h6>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div class="mb-2">
                <label class="form-label form-label-sm">GL Account<span class="text-danger"> *</span></label>
                <input type="text" name="ledger_account" id="edit_ledger_account" class="form-control form-control-sm" required>
            </div>
            <div class="mb-2">
                <label class="form-label form-label-sm">Desc COA<span class="text-danger"> *</span></label>
                <input type="text" name="desc_coa" id="edit_desc_coa" class="form-control form-control-sm" required>
            </div>
            <div class="mb-2">
                <label class="form-label form-label-sm">Tax Percent<span class="text-danger"> *</span></label>
                <input type="number" name="tax_percent" class="form-control form-control-sm" required>
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
    const table = $('#ledgerAccountTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('admin.ledger-account.index') }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'ledger_account', name: 'ledger_account' },
            { data: 'desc_coa', name: 'desc_coa' },
            { data: 'tax_percent', name: 'tax_percent' },
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
            url: "{{ route('admin.ledger-account.store') }}",
            method: "POST",
            data: $(this).serialize(),
            success: function(res){
                $('#addModal').modal('hide');
                $('#ledgerAccountTable').DataTable().ajax.reload();
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
        $.get("{{ route('admin.ledger-account.show', ':id') }}".replace(':id', id), function (data) {
            $('#edit_id').val(data.id);
            $('#edit_ledger_account').val(data.ledger_account);
            $('#edit_desc_coa').val(data.desc_coa);
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
            url: "{{ url('admin/master-data/ledger-account') }}/" + id,
            method: "PUT",
            data: $(this).serialize(),
            success: function(res){
                $('#editModal').modal('hide');
                $('#ledgerAccountTable').DataTable().ajax.reload();
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
                    url: "{{ route('admin.ledger-account.destroy', ':id') }}".replace(':id', id),
                    method: "POST",
                    data: {
                        _method: 'DELETE',
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
                        $('#ledgerAccountTable').DataTable().ajax.reload();
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