@extends('layouts.app')

@section('content')
<div class="card shadow-sm">
    <div class="card-header py-2 d-flex justify-content-between align-items-center">
        <h6 class="mb-0 text-muted">Vendor</h6>
        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
            Add Vendor
        </button>
    </div>
    <div class="card-body p-2">
        <table id="vendorTable" class="table table-bordered table-hover table-sm text-sm w-100">
            <thead class="table-light small">
                <tr>
                    <th style="width: 50px;">No</th>
                    <th>Name</th>
                    <th>Vendor Number</th>
                    <th>Cost Center</th>
                    <th>Type</th>
                    <th>GL Accounts</th>
                    <th style="width: 120px;">Action</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered"> <!-- ubah ke modal-lg -->
      <div class="modal-content">
        <form id="addVendorForm" method="POST" action="{{ route('admin.vendor.store') }}">
            @csrf
          <div class="modal-header bg-light py-2">
            <h6 class="modal-title fw-bold">Add Vendor</h6>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
  
          <div class="modal-body">
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label form-label-sm fw-semibold">Name<span class="text-danger"> *</span></label>
                <input type="text" name="name" class="form-control form-control-sm" required>
              </div>
  
              <div class="col-md-6">
                <label class="form-label form-label-sm fw-semibold">Vendor Number<span class="text-danger"> *</span></label>
                <input type="text" name="vendor_number" class="form-control form-control-sm" required>
              </div>
  
              <div class="col-md-6">
                <label class="form-label form-label-sm fw-semibold">Cost Center<span class="text-danger"> *</span></label>
                <select name="cost_center" class="form-select form-select-sm" required>
                  <option value="" disabled selected>Pilih Cost Center</option>
                  <option value="11501">11501</option>
                  <option value="11601">11601</option>
                </select>
              </div>
              
  
              <div class="col-md-6">
                <label class="form-label form-label-sm fw-semibold">Type<span class="text-danger"> *</span></label>
                <select name="type_id" class="form-select form-select-sm" required>
                  <option value="" selected disabled>-- Select Type --</option>
                  @foreach($types as $type)
                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                  @endforeach
                </select>
              </div>
  
              <div class="col-12">
                <label class="form-label form-label-sm fw-semibold">GL Account</label>
                <select name="ledger_account_id" id="add_ledger_account" class="form-control form-control-sm">
                    <option value="">-- Select GL Account --</option>
                    @foreach($ledgerAccounts as $account)
                        <option value="{{ $account->id }}">
                            {{ $account->ledger_account }} - {{ $account->desc_coa }}
                        </option>
                    @endforeach
                </select>                              
              </div>
            </div>
          </div>
  
          <div class="modal-footer py-2 bg-light">
            <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-sm btn-primary">Save</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  

<!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">
            <form method="POST" id="editVendorForm">
            @csrf
            @method('PUT')
            <input type="hidden" name="id" id="edit_id">
    
            <div class="modal-header">
                <h6 class="modal-title">Edit Vendor</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
    
            <div class="modal-body">
                <div class="row">
                <div class="col-md-6 mb-2">
                    <label class="form-label form-label-sm">Name<span class="text-danger"> *</span></label>
                    <input type="text" name="name" id="edit_name" class="form-control form-control-sm" required>
                </div>
    
                <div class="col-md-6 mb-2">
                    <label class="form-label form-label-sm">Vendor Number<span class="text-danger"> *</span></label>
                    <input type="text" name="vendor_number" id="edit_vendor_number" class="form-control form-control-sm" required>
                </div>
    
                <div class="col-md-6 mb-2">
                    <label class="form-label form-label-sm">Cost Center<span class="text-danger"> *</span></label>
                    <select name="cost_center" id="edit_cost_center" class="form-select form-select-sm" required>
                    <option value="">-- Pilih Cost Center --</option>
                    <option value="11501">11501</option>
                    <option value="11601">11601</option>
                    </select>
                </div>
    
                <div class="col-md-6 mb-2">
                    <label class="form-label form-label-sm">Type<span class="text-danger"> *</span></label>
                    <select name="type_id" id="edit_type_id" class="form-select form-select-sm" required>
                    <option value="">-- Pilih Type --</option>
                    @foreach($types as $type)
                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                    @endforeach
                    </select>
                </div>
    
                    <div class="col-md-12 mb-2">
                        <label class="form-label form-label-sm">GL Account</label>
                        <select name="ledger_account_id" id="edit_ledger_accounts" class="form-control form-control-sm">
                            @foreach($ledgerAccounts as $account)
                                <option value="{{ $account->id }}">
                                    {{ $account->ledger_account }} - {{ $account->desc_coa }}
                                </option>
                            @endforeach
                        </select>                    
                    </div>
                </div>
            </div>
    
            <div class="modal-footer py-1">
                <button type="submit" class="btn btn-sm btn-success">Update</button>
            </div>
            </form>
        </div>
        </div>
    </div>
  
  
  @if (session('success'))
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Success',
            text: '{{ session('success') }}',
            confirmButtonColor: '#3085d6'
        });
    </script>
    @endif

    @if (session('error'))
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: '{{ session('error') }}',
            confirmButtonColor: '#d33'
        });
    </script>
    @endif

@endsection


@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {
    new TomSelect('#add_ledger_account', {
        placeholder: 'Select GL Account...',
        allowEmptyOption: true
    });

    new TomSelect('#edit_ledger_accounts', {
        placeholder: 'Select GL Account...',
        allowEmptyOption: true
    });
});
</script>

<script>
    $('#vendorTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('admin.vendor.index') }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'name', name: 'name' },
            { data: 'vendor_number', name: 'vendor_number' },
            { data: 'cost_center', name: 'cost_center' },
            { data: 'type_name', name: 'type_name' },
            { data: 'ledger_accounts', name: 'ledger_accounts' },
            {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false,
                className: 'text-center'
            }
        ]
    });


    // Setup CSRF Token untuk semua request AJAX
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    document.addEventListener("DOMContentLoaded", function () {
        new TomSelect('#edit_ledger_accounts', {
            placeholder: 'Select GL Account...',
            allowEmptyOption: true
        });
    });
    
    $(document).on('click', '.btn-edit', function () {
        const id = $(this).data('id');
        const form = $('#editVendorForm');
        const modal = $('#editModal');
        const ledgerSelect = document.getElementById('edit_ledger_accounts');

        const updateUrl = "{{ route('admin.vendor.update', ':id') }}".replace(':id', id);
        form.attr('action', updateUrl);

        const fetchUrl = "{{ route('admin.vendor.show', ':id') }}".replace(':id', id);
        $.get(fetchUrl)
            .done(function (data) {
                console.log(data);
            fillEditForm(data);
            modal.modal('show');
            })
            .fail(function (xhr) {
            console.error(xhr);
            alert('Gagal mengambil data. Silakan coba lagi.');
            });

            function fillEditForm(data) {
            $('#edit_id').val(data.id);
            $('#edit_name').val(data.name);
            $('#edit_vendor_number').val(data.vendor_number);

            $('#edit_cost_center').val(data.cost_center || '');
            $('#edit_type_id').val(data.em_type_id || '');

            // Atur ledger account ke select multiple
            const ledgerId = String(data.ledger_account_id || '');
            if (ledgerSelect.tomselect) {
                ledgerSelect.tomselect.setValue(ledgerId);
            } else {
                $('#edit_ledger_accounts').val(ledgerId);
            }


        }

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
                    url: "{{ route('admin.vendor.destroy', ':id') }}".replace(':id', id),
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
                        $('#vendorTable').DataTable().ajax.reload();
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