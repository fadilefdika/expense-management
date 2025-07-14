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
                    <th>Created Date</th>
                    <th>unique</th>
                    <th>Description</th>
                    <th>Nominal(Rp)</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="advanceModal" tabindex="-1" aria-labelledby="advanceModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <form action="{{ route('admin.advance.store') }}" method="POST" class="w-100">
      @csrf
      <div class="modal-content shadow-lg rounded-4 border-0">
        <div class="modal-header border-0 pb-0">
          <h5 class="modal-title fw-semibold text-primary" id="advanceModalLabel">
            Tambah Advance / PR-Online
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body py-4 px-4">
          <div class="row g-3">
            <div class="col-md-6">
              <label for="main_type" class="form-label fw-semibold">Main Type<span class="text-danger"> *</span></label>
              <select name="main_type" id="main_type" class="form-select rounded-3 shadow-sm" required>
                <option value="">-- Pilih Main Type --</option>
                <option value="Advance">Advance</option>
                <option value="PR-Online">PR-Online</option>
              </select>
            </div>

            <div class="col-md-6">
              <label for="sub_type_advance" class="form-label fw-semibold">Type<span class="text-danger"> *</span></label>
              <select name="sub_type_advance" id="sub_type_advance" class="form-select rounded-3 shadow-sm" required>
                <option value="">-- Pilih Type --</option>
              </select>
            </div>

            <div class="col-md-6">
              <label for="date_advance" class="form-label fw-semibold">Tanggal Pembuatan<span class="text-danger"> *</span></label>
              <div id="datepicker-wrapper" class="position-relative">
                <input type="datetime-local" name="date_advance" id="date_advance" class="form-control rounded-3 shadow-sm" required>
              </div>
            </div>

            <div class="col-md-6">
              <label for="nominal_display" class="form-label fw-semibold">Nominal<span class="text-danger"> *</span></label>
              <div class="input-group">
                <span class="input-group-text bg-light border-end-0 rounded-start-3">Rp</span>
                <input type="text" id="nominal_display" class="form-control border-start-0 rounded-end-3 shadow-sm" required autocomplete="off">
              </div>
              <input type="hidden" name="nominal_advance" id="nominal_advance">
            </div>

            <div class="col-12">
              <label for="description" class="form-label fw-semibold">Description<span class="text-danger"> *</span></label>
              <textarea name="description" id="description" class="form-control rounded-3 shadow-sm" rows="3" required placeholder="Tulis deskripsi singkat..."></textarea>
            </div>
          </div>
        </div>

        <div class="modal-footer border-0 pt-0 px-4 pb-4">
          <button type="button" class="btn btn-light border px-4 rounded-3 shadow-sm" data-bs-dismiss="modal">
            Batal
          </button>
          <button type="submit" class="btn btn-primary px-4 rounded-3 shadow-sm">
            Simpan
          </button>
        </div>
      </div>
    </form>
  </div>
</div>

  
  
@endsection

@push('scripts')
    <!-- Script Dinamis -->
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        const mainType = document.getElementById('main_type');
        const subType = document.getElementById('sub_type_advance');
        const nominalDisplay = document.getElementById('nominal_display');
        const nominal = document.getElementById('nominal');
        const wrapper = document.getElementById('datepicker-wrapper');
        const dateInput = document.getElementById('date_advance');
    
        const typeOptions = {
          'Advance': [
            { value: 'GAA', text: 'GAA' },
            { value: 'HRA', text: 'HRA' }
          ],
          'PR-Online': [
            { value: 'GAO', text: 'GAO' },
            { value: 'HRO', text: 'HRO' }
          ]
        };
    
        // Disable subType saat awal
        subType.disabled = true;
    
        mainType.addEventListener('change', function () {
          const selected = this.value;
          subType.innerHTML = '<option value="">-- Pilih Type --</option>';
    
          if (typeOptions[selected]) {
            subType.disabled = false;
            typeOptions[selected].forEach(opt => {
              const option = document.createElement('option');
              option.value = opt.value;
              option.text = opt.text;
              subType.appendChild(option);
            });
          } else {
            subType.disabled = true;
          }
        });
    
        // Fokus datetime picker saat klik wrapper
        wrapper.addEventListener('click', function () {
          dateInput.focus();
          if (typeof dateInput.showPicker === 'function') {
            dateInput.showPicker();
          }
        });
    
        // Tutup picker setelah memilih
        dateInput.addEventListener('change', function () {
          dateInput.blur();
        });
    
        // Format nominal input ke ribuan
        nominalDisplay.addEventListener('input', function () {
          let raw = this.value.replace(/\D/g, '');
          let formatted = raw.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    
          this.value = formatted;
          nominal.value = raw;
        });
      });
    
      // Inisialisasi DataTables
      $(function () {
        $('#advanceTable').DataTable({
          processing: true,
          serverSide: true,
          ajax: '{{ route('admin.advance.index') }}',
          columns: [
            {
              data: 'DT_RowIndex',
              name: 'DT_RowIndex',
              orderable: false,
              searchable: false,
              className: 'text-sm'
            },
            {
              data: 'date_advance',
              name: 'date_advance', // ganti name jadi field sebenarnya
              className: 'text-sm'
            },
            {
              data: 'code_advance',
              name: 'code_advance', // ganti name jadi field sebenarnya
              className: 'text-sm'
            },
            {
              data: 'description',
              name: 'description',
              className: 'text-sm'
            },
            {
              data: 'nominal_advance',
              name: 'nominal_advance',
              className: 'text-sm'
            }
          ],
          order: [[1, 'desc']], // urut berdasarkan tanggal
          language: {
            emptyTable: "Belum ada data advance",
            processing: "Memuat...",
            lengthMenu: "Tampilkan _MENU_ data",
            search: "Cari:",
            paginate: {
              first: "Awal",
              last: "Akhir",
              next: "›",
              previous: "‹"
            },
            zeroRecords: "Data tidak ditemukan",
          }
        });
      });
    </script>
    

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const nominalInput = document.getElementById('nominal_display');
            const hiddenInput = document.getElementById('nominal_advance');
        
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
    </>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const wrapper = document.getElementById('datepicker-wrapper');
            const input = document.getElementById('date_advance');
    
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
