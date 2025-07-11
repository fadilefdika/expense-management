@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Data Advance</h5>
        <!-- Tombol untuk membuka modal -->
        <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#advanceModal">
            Tambah Advance
        </button>
    </div>
    <div class="card-body">
        <table class="table table-bordered table-hover" id="advanceTable">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal Dibuat</th>
                    <th>Kode Unik</th>
                    <th>Deskripsi</th>
                    <th>Nominal(Rp)</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="advanceModal" tabindex="-1" aria-labelledby="advanceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <form action="{{ route('admin.advance.store') }}" method="POST" class="w-100">
        @csrf
        <div class="modal-content shadow rounded-4 border-0">
          <div class="modal-header border-0 pb-0">
            <h5 class="modal-title fw-semibold text-primary" id="advanceModalLabel">Tambah Advance</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
  
          <div class="modal-body py-3">
            <div class="mb-3">
              <label for="type" class="form-label fw-semibold">Type<span class="text-danger"> *</span></label>
              <select name="type" id="type" class="form-select rounded-3 shadow-sm" required>
                <option value="">-- Pilih Type --</option>
                <option value="GAA">GAA</option>
                <option value="HRA">HRA</option>
              </select>
            </div>
  
            <div class="mb-3">
                <label for="date" class="form-label fw-semibold">
                  Tanggal Pembuatan<span class="text-danger"> *</span>
                </label>
              
                <div id="datepicker-wrapper" style="cursor: text;">
                  <input
                    type="datetime-local"
                    name="date"
                    id="date"
                    class="form-control rounded-3 shadow-sm"
                    required
                  >
                </div>
              </div>
  
            <div class="mb-3">
              <label for="description" class="form-label fw-semibold">Deskripsi<span class="text-danger"> *</span></label>
              <textarea name="description" id="description" class="form-control rounded-3 shadow-sm" rows="3" required></textarea>
            </div>
  
            <div class="mb-3">
                <label for="nominal_display" class="form-label fw-semibold">Nominal<span class="text-danger"> *</span></label>
                <div class="input-group">
                  <span class="input-group-text bg-light border-end-0 rounded-start-3">Rp</span>
                  <input type="text" id="nominal_display"
                         class="form-control border-start-0 rounded-end-3 shadow-sm"
                         required autocomplete="off">
                </div>
                <input type="hidden" name="nominal" id="nominal"> {{-- nilai asli yang dikirim --}}
              </div>             
          </div>
  
          <div class="modal-footer border-0 pt-0">
            <button type="button" class="btn btn-light border shadow-sm px-4" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary px-4 shadow-sm">Simpan</button>
          </div>
        </div>
      </form>
    </div>
  </div>
  
  
@endsection

@push('scripts')
    <script>
        $(function () {
            $('#advanceTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('admin.advance.index') }}',
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-sm' },
                    { data: 'date', name: 'created_at',className: 'text-sm' },
                    { data: 'code', name: 'kode_unik',className: 'text-sm' },
                    { data: 'description', name: 'description',className: 'text-sm' },
                    { data: 'nominal', name: 'nominal',className: 'text-sm' },
                ]
            });
        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const nominalInput = document.getElementById('nominal_display');
            const hiddenInput = document.getElementById('nominal');
        
            nominalInput.addEventListener('input', function () {
                let value = nominalInput.value.replace(/\./g, '').replace(/\D/g, '');
                if (!value) {
                    hiddenInput.value = '';
                    nominalInput.value = '';
                    return;
                }
        
                hiddenInput.value = value;
        
                nominalInput.value = value.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const wrapper = document.getElementById('datepicker-wrapper');
            const input = document.getElementById('date');
    
            // Fokuskan input ketika wrapper diklik
            wrapper.addEventListener('click', function () {
                input.focus();
    
                // Workaround untuk membuka datetime picker di sebagian browser
                if (typeof input.showPicker === 'function') {
                    input.showPicker(); // Chrome-based only
                }
            });
        });
    </script>
@endpush
