@extends('layouts.app')

@section('content')
<div class="card shadow-sm rounded-4 border-0">
    <div class="card-header bg-white border-bottom py-2 px-4 d-flex justify-content-between align-items-center">
        <h6 class="mb-0 text-muted fw-semibold" style="font-size: 15px;">
            Input Expense
        </h6>
    </div>

    <form action="{{ route('admin.advance.store') }}" method="POST">
        @csrf
        <div class="card-body py-3 px-4">
            <div class="row g-3">
                {{-- Main Type --}}
                <div class="col-md-6">
                    <label for="main_type" class="form-label form-label-sm">Main Type</label>
                    <select name="main_type" id="main_type" class="form-select form-select-sm" required>
                        <option value="">-- Select Main Type --</option>
                        <option value="advance">Advance</option>
                        <option value="pr_online">PR Online</option>
                    </select>
                </div>
            </div>

            {{-- SECTION: ADVANCE --}}
            <div id="advance-section" class="mt-4 d-none">
                <div class="row g-2">
                    <div class="col-md-4">
                        <label class="form-label form-label-sm">TYPE</label>
                        <select name="type_advance" class="form-select form-select-sm">
                            <option value="">-- Select Type --</option>
                            @foreach($typeAdvance as $t)
                                <option value="{{ $t->id }}">{{ $t->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label form-label-sm">Submitted Date</label>
                        <input type="date" name="submitted_date" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label form-label-sm">Nominal</label>
                        <input type="text" name="nominal_advance" id="nominal_advance" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-12">
                        <label class="form-label form-label-sm">Description</label>
                        <textarea name="description_advance" rows="2" class="form-control form-control-sm"></textarea>
                    </div>
                </div>
            </div>

            {{-- SECTION: PR ONLINE --}}
            <div id="pr-section" class="mt-4 d-none">
                <div class="row g-2">
                    <div class="col-md-4">
                        <label class="form-label form-label-sm">TYPE</label>
                        <select name="type_advance" class="form-select form-select-sm">
                            <option value="">-- Select Type --</option>
                            @foreach($typePR as $t)
                                <option value="{{ $t->id }}">{{ $t->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label form-label-sm">Submitted Date</label>
                        <input type="date" name="submitted_date" class="form-control form-control-sm">
                    </div>
                    {{-- Vendor Name --}}
                    <div class="col-md-12">
                        <label for="vendor_name" class="form-label form-label-sm">Vendor Name</label>
                        <input type="text" name="vendor_name" id="vendor_name" class="form-control form-control-sm" required>
                    </div>

                    {{-- Expense --}}
                    <div class="col-md-6">
                        <label class="form-label form-label-sm">Expense Type</label>
                        <select name="expense_type" id="expense_type" class="form-select form-select-sm" required>
                            <option value="">-- Select Type --</option>
                            @foreach ($expenseTypes as $type)
                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label form-label-sm">Expense Category</label>
                        <select name="expense_category" id="expense_category" class="form-select form-select-sm" required disabled>
                            <option value="">-- Select Category --</option>
                            @foreach ($expenseCategories as $cat)
                                <option value="{{ $cat->id }}" data-type="{{ $cat->expense_type_id }}">
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label form-label-sm">Nominal</label>
                        <input type="text" name="nominal_advance" id="nominal_advance" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-12">
                        <label class="form-label form-label-sm">Description</label>
                        <textarea name="description_advance" rows="2" class="form-control form-control-sm"></textarea>
                    </div>
                    {{-- Table Detail Penggunaan --}}
                    <div class="mt-4">
                        <label class="form-label form-label-sm">Usage Details</label>
                        <table class="table table-bordered table-sm" id="rincianTable">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 40px;">No</th>
                                    <th>Description</th>
                                    <th style="width: 80px;">Qty</th>
                                    <th style="width: 120px;">Nominal</th>
                                    <th style="width: 120px;">Total</th>
                                    <th style="width: 40px;"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td><input type="text" name="items[0][description]" class="form-control form-control-sm"></td>
                                    <td><input type="number" name="items[0][qty]" class="form-control form-control-sm qty" min="1" value="1"></td>
                                    <td><input type="number" name="items[0][nominal]" class="form-control form-control-sm nominal" min="0"></td>
                                    <td><input type="text" class="form-control form-control-sm total" readonly></td>
                                    <td><button type="button" class="btn btn-sm btn-danger remove-item">&times;</button></td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="4" class="text-end">Jumlah Total</th>
                                    <th><input type="text" id="grandTotal" class="form-control form-control-sm" readonly></th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                        <button type="button" class="btn btn-sm btn-secondary mt-2" id="addItem">Add Item</button>
                    </div>
                </div>
            </div>
            <div class="mt-4 text-end">
                <button type="submit" class="btn btn-sm btn-primary">Submit</button>
            </div>
        </div>
    </form>
</div>
@endsection


@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const typeSelect = document.getElementById('expense_type');
        const categorySelect = document.getElementById('expense_category');

        function filterCategories() {
            const selectedTypeId = typeSelect.value;
            const currentCategory = categorySelect.value;

            if (!selectedTypeId) {
                categorySelect.disabled = true;
                return;
            }

            categorySelect.disabled = false;

            let foundMatch = false;

            Array.from(categorySelect.options).forEach(option => {
                if (!option.value) return; // skip placeholder
                const type = option.getAttribute('data-type');
                const isVisible = type === selectedTypeId;

                option.hidden = !isVisible;

                if (isVisible && option.value === currentCategory) {
                    foundMatch = true;
                }
            });

            // Jika current category tidak cocok, reset
            if (!foundMatch) {
                categorySelect.value = '';
            }
        }

        typeSelect.addEventListener('change', filterCategories);

        // Jalankan filter di awal
        filterCategories();
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const table = document.getElementById('rincianTable').getElementsByTagName('tbody')[0];
        const nominalAdvanceInput = document.getElementById('nominal_advance');
        const nominalSettlementInput = document.getElementById('nominal_settlement');
        const differenceInput = document.getElementById('difference');

        function formatRupiah(angka) {
            return angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }

        function parseNumber(str) {
            return parseFloat(str.replace(/\./g, '')) || 0;
        }

        function updateTotal(row) {
            const qty = parseFloat(row.querySelector('.qty')?.value) || 0;
            const nominal = parseFloat(row.querySelector('.nominal')?.value) || 0;
            const total = qty * nominal;
            row.querySelector('.total').value = formatRupiah(total);
            updateGrandTotal();
        }

        function updateGrandTotal() {
            let total = 0;
            document.querySelectorAll('.total').forEach(input => {
                total += parseNumber(input.value);
            });

            // Update Grand Total input raw number
            document.getElementById('grandTotal').value = formatRupiah(total);

            // Update Nominal Settlement (formatted)
            nominalSettlementInput.value = formatRupiah(total);

            // Hitung selisih
            const advance = parseNumber(nominalAdvanceInput.value);
            const difference = advance - total;
            differenceInput.value = formatRupiah(difference);
        }


        document.getElementById('addItem').addEventListener('click', function () {
            const rowCount = table.rows.length;
            const newRow = table.insertRow();
            newRow.innerHTML = `
                <td>${rowCount + 1}</td>
                <td><input type="text" name="items[${rowCount}][description]" class="form-control form-control-sm"></td>
                <td><input type="number" name="items[${rowCount}][qty]" class="form-control form-control-sm qty" min="1" value="1"></td>
                <td><input type="number" name="items[${rowCount}][nominal]" class="form-control form-control-sm nominal" min="0"></td>
                <td><input type="text" class="form-control form-control-sm total" readonly></td>
                <td><button type="button" class="btn btn-sm btn-danger remove-item">&times;</button></td>
            `;
        });

        document.addEventListener('input', function (e) {
            if (e.target.classList.contains('qty') || e.target.classList.contains('nominal')) {
                const row = e.target.closest('tr');
                updateTotal(row);
            }
        });

        document.addEventListener('click', function (e) {
            if (e.target.classList.contains('remove-item')) {
                const row = e.target.closest('tr');
                row.remove();
                renumberRows();
                updateGrandTotal();
            }
        });

        function renumberRows() {
            const rows = table.querySelectorAll('tr');
            rows.forEach((row, index) => {
                row.querySelector('td:first-child').textContent = index + 1;
            });
        }

    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const mainType = document.getElementById('main_type');
        const advanceSection = document.getElementById('advance-section');
        const prSection = document.getElementById('pr-section');
    
        function toggleSections() {
            const value = mainType.value;
            advanceSection.classList.toggle('d-none', value !== 'advance');
            prSection.classList.toggle('d-none', value !== 'pr_online');
        }
    
        mainType.addEventListener('change', toggleSections);
    });
    </script>
@endpush