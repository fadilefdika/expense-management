@extends('layouts.app')

@section('content')
<div class="card border-0 shadow rounded-4 bg-white">
  <div class="card-header border-bottom d-flex justify-content-between align-items-center py-3 px-4 bg-transparent">
      <h6 class="mb-0 fw-semibold text-muted">Data Advance</h6>
      <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#advanceModal">
          Add Advance
      </button>
  </div>
  <div class="card-body px-0 pt-2 pb-3">
      <div class="table-responsive px-3">
          <table class="table table-sm align-middle notion-table" id="advanceTable">
              <thead class="table-light">
                  <tr>
                      <th>No</th>
                      <th>Created Date</th>
                      <th>Unique Code</th>
                      <th>Description</th>
                      <th>Nominal (Rp)</th>
                  </tr>
              </thead>
          </table>
      </div>
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
            Input Expense
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body py-4 px-4">
          <div class="row g-3">
            <div class="col-md-6">
              <label for="main_type" class="form-label fw-semibold">Main Type<span class="text-danger"> *</span></label>
              <select name="main_type" id="main_type" class="form-select rounded-3 shadow-sm" required>
                <option value="">-- Select Main Type --</option>
                <option value="Advance">Advance</option>
                <option value="PR-Online">PR-Online</option>
              </select>
            </div>

            <div class="col-md-6">
              <label for="sub_type_advance" class="form-label fw-semibold">Type<span class="text-danger"> *</span></label>
              <select name="sub_type_advance" id="sub_type_advance" class="form-select rounded-3 shadow-sm" required>
                <option value="">-- Select Type --</option>
              </select>
            </div>

            <div class="col-md-6">
              <label for="date_advance" class="form-label fw-semibold">Submitted Date<span class="text-danger"> *</span></label>
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
        padding: 0.6rem 0.75rem;
    }


    .notion-table tbody tr {
        border-bottom: 1px solid #f1f1f1;
    }

    .notion-table tbody tr:hover {
        background-color: #f8f9fc;
    }

    /* Wrap text for Description */
    .notion-table td:nth-child(4) {
        max-width: 200px;
        white-space: normal;
        word-break: break-word;
    }

    /* Keep unique code in one line */
    .notion-table td:nth-child(3) {
        white-space: nowrap;
        font-size: 10px;
        color: #374151;
    }

    .btn-sm {
        font-size: 10px;
        padding: 4px 10px;
        border-radius: 6px;
    }

    .btn-primary {
        background-color: #3b82f6;
        border-color: #3b82f6;
    }

    .btn-primary:hover {
        background-color: #2563eb;
        border-color: #2563eb;
    }
</style>
@endpush


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
          subType.innerHTML = '<option value="">-- Select Type --</option>';
    
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
        if ($.fn.DataTable.isDataTable('#advanceTable')) {
            $('#advanceTable').DataTable().destroy();
        }
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
    </script>

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
