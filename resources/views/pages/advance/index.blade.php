@extends('layouts.app')

@section('content')
<div class="card border-0 shadow rounded-4 bg-white">
  <div class="card-header border-bottom d-flex justify-content-between align-items-center py-3 px-4 bg-transparent">
    <h6 class="mb-0 fw-semibold text-muted">Data Expense</h6>

    <div class="d-flex gap-2">
        <a href="{{ route('admin.advance.create') }}" class="btn btn-primary btn-sm">
            Add Expense
        </a>
        <a href="{{ route('admin.advance.export-excel') }}" class="btn btn-outline-success btn-sm">
            Export
        </a>
    </div>
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

    .dataTables_wrapper .dataTables_scroll {
        overflow: visible !important;
    }

    table.dataTable {
        width: 100% !important;
    }

    /* DATATABLE */
    .dataTables_length label {
        font-size: 11px;
    }

    /* Search input */
    .dataTables_filter label {
        font-size: 11px;
    }

    /* Info text: 'Showing 1 to 10 of 100 entries' */
    .dataTables_info {
        font-size: 11px;
    }

    /* Pagination buttons */
    .dataTables_paginate {
        font-size: 11px;
    }

    .dataTables_paginate .paginate_button {
        font-size: 11px;
    }
    /* Ukuran font & padding tombol pagination */
    .dataTables_wrapper .dataTables_paginate .paginate_button {
        font-size: 12px !important; /* naikkan dari 10px */
        min-width: auto !important;
        border-radius: 6px !important;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button.page-item .page-link,
    .dataTables_wrapper .dataTables_paginate .paginate_button .page-link {
        font-size: 12px !important;
        padding: 4px 10px !important;
        border-radius: 6px !important;
        min-width: auto !important;
        line-height: 1.2 !important;
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
          scrollX: true,
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
