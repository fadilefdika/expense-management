<form action="{{ route('admin.settlement.update', $advance->id) }}" method="POST">

    @csrf

    <div class="row g-3">

        {{-- Kode --}}
        <div class="col-md-6">
            <label for="code_advance" class="form-label text-dark fw-semibold small">Advance Code</label>
            <input type="text" name="code_advance" id="code_advance"
                class="form-control form-control-sm shadow-sm"
                style="font-size: 11px;"
                value="{{ old('code_advance', $advance->code_advance ?? $noAdvance ?? '') }}" readonly>
        </div>
    
        <div class="col-md-6">
            <label for="code_settlement" class="form-label text-dark fw-semibold small">Settlement Code</label>
            <input type="text" name="code_settlement" id="code_settlement"
                class="form-control form-control-sm shadow-sm"
                style="font-size: 11px;"
                value="{{ old('code_settlement', $advance->code_settlement ?? $codeSettlement ?? '') }}" readonly>
        </div>
    
        {{-- Vendor Name --}}
        <div class="col-md-6">
            <label for="vendor_id" class="form-label text-dark fw-semibold small">Vendor Name</label>
            <select name="vendor_id" id="vendor_id"
                class="form-select form-select-sm shadow-sm"
                style="font-size: 5px;" required>
                <option value="">-- Select Vendor --</option>
                @foreach($vendors as $vendor)
                    <option 
                        value="{{ $vendor->id }}" 
                        data-type="{{ $vendor->em_type_id }}" 
                        @selected(old('vendor_id', $selectedVendor) == $vendor->id)>
                        {{ $vendor->name }}
                    </option>
                @endforeach
            </select>
        </div>
        

        {{-- PO / Invoice Number --}}
        <div class="col-md-6">
            <label class="form-label text-dark fw-semibold small">PO / Invoice Number<span class="text-danger"> *</span></label>
            <input type="number" name="invoice_number" id="invoice_number" class="form-control form-control-sm" placeholder="Optional" value="{{ old('invoice_number', $advance->invoice_number ?? '') }}">
        </div>
    
        {{-- Expense Type --}}
        <div class="col-md-6">
            <label for="expense_type" class="form-label text-dark fw-semibold small">Expense Type</label>
            <select name="expense_type" id="expense_type"
                class="form-select form-select-sm shadow-sm"
                style="font-size: 11px;" required>
                <option value="">-- Select Type --</option>
                @foreach ($expenseTypes as $type)
                    <option value="{{ $type->id }}"
                        @selected(old('expense_type', $advance->expense_type ?? '') == $type->id)>
                        {{ $type->name }}
                    </option>
                @endforeach
            </select>
        </div>
    
        {{-- Expense Category --}}
        <div class="col-md-6">
            <label for="expense_category" class="form-label text-dark fw-semibold small">Expense Category</label>
            <select name="expense_category" id="expense_category"
                class="form-select form-select-sm shadow-sm"
                style="font-size: 11px;" required>
                <option value="">-- Select Category --</option>
            </select>
        </div>
    
        {{-- Nominal --}}
        <div class="col-md-4">
            <label for="nominal_advance" class="form-label text-dark fw-semibold small">Nominal Advance (Rp)</label>
            <input type="text" name="nominal_advance" id="nominal_advance"
                class="form-control form-control-sm shadow-sm"
                style="font-size: 11px;"
                value="{{ number_format(old('nominal_advance', $advance->nominal_advance ?? 0), 0, ',', '.') }}" readonly>
        </div>
    
        <div class="col-md-4">
            <label for="nominal_settlement" class="form-label text-dark fw-semibold small">Nominal Settlement (Rp)</label>
            <input type="text" name="nominal_settlement" id="nominal_settlement"
                class="form-control form-control-sm shadow-sm"
                style="font-size: 11px;"
                value="{{ number_format(old('nominal_settlement', $advance->nominal_settlement ?? 0), 0, ',', '.') }}" readonly>
        </div>
    
        <div class="col-md-4">
            <label for="difference" class="form-label text-dark fw-semibold small">Difference (Rp)</label>
            <input type="text" name="difference" id="difference"
                class="form-control form-control-sm shadow-sm"
                style="font-size: 11px;"
                value="{{ number_format(old('difference', $advance->difference ?? 0), 0, ',', '.') }}" readonly>
        </div>
    
        {{-- Deskripsi --}}
        <div class="col-md-12">
            <label for="description" class="form-label text-dark fw-semibold small">Description</label>
            <textarea name="description" id="description" rows="2"
                class="form-control form-control-sm shadow-sm"
                style="font-size: 11px;" required>{{ old('description', $advance->description ?? '') }}</textarea>
        </div>
    
    </div>
    
    
    {{-- Tabel Rincian Penggunaan --}}
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
                @php
                    $items = old('items', $advance->settlementItems ?? []);
                @endphp

                @forelse ($items as $i => $item)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>
                            <input type="text" name="items[{{ $i }}][description]" class="form-control form-control-sm" style="font-size: 11px;"
                                value="{{ old("items.$i.description", $item['description'] ?? '') }}">
                        </td>
                        <td>
                            <input type="number" name="items[{{ $i }}][qty]" class="form-control form-control-sm qty" style="font-size: 11px;" min="1"
                                value="{{ old("items.$i.qty", $item['qty'] ?? 1) }}">
                        </td>
                        <td>
                            <input type="number" name="items[{{ $i }}][nominal]" class="form-control form-control-sm nominal" style="font-size: 11px;" min="0"
                                value="{{ old("items.$i.nominal", $item['nominal'] ?? 0) }}">
                        </td>
                        <td>
                            <input type="number" class="form-control form-control-sm total" style="font-size: 11px;"
                                value="{{ ($item['qty'] ?? 1) * ($item['nominal'] ?? 0) }}" readonly>
                        </td>
                        <td>
                            <button type="button" class="btn btn-sm btn-danger remove-item">&times;</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td>1</td>
                        <td><input type="text" name="items[0][description]" class="form-control form-control-sm" style="font-size: 11px;"></td>
                        <td><input type="number" name="items[0][qty]" class="form-control form-control-sm qty" style="font-size: 11px;" min="1" value="1"></td>
                        <td><input type="number" name="items[0][nominal]" class="form-control form-control-sm nominal" style="font-size: 11px;" min="0"></td>
                        <td><input type="number" class="form-control form-control-sm total" style="font-size: 11px;" readonly></td>
                        <td><button type="button" class="btn btn-sm btn-danger remove-item">&times;</button></td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="4" class="text-end">Jumlah Total</th>
                    <th><input type="text" id="grandTotal" class="form-control form-control-sm" readonly></th>
                    <th></th>
                </tr>
            </tfoot>
        </table>
        <button type="button" class="btn btn-sm btn-secondary" id="addItem">Add Item</button>
    </div>


    <div class="mt-3 text-end">
        <button type="submit" class="btn btn-sm btn-primary">Submit</button>
    </div>
</form>

@push('scripts')
<script>
    const rawCategories = @json($expenseCategories);

    document.addEventListener('DOMContentLoaded', function () {
        const expenseTypeEl = document.getElementById('expense_type');
        const expenseCategoryEl = document.getElementById('expense_category');

        // Inisialisasi Tom Select untuk Category
        const categorySelect = new TomSelect(expenseCategoryEl, {
            create: false,
            placeholder: 'Select Category',
        });

        // Inisialisasi Tom Select untuk Type
        const typeSelect = new TomSelect(expenseTypeEl, {
            create: false,
            placeholder: 'Select Type',
            onChange(value) {
                updateCategoryOptions(value);
            }
        });

        // Inisialisasi Tom Select untuk Vendor
        new TomSelect('#vendor_id', {
            create: false,
            sortField: { field: "text", direction: "asc" },
            placeholder: '-- Select Vendor --',
        });

        // Fungsi update kategori berdasarkan type
        function updateCategoryOptions(selectedTypeId) {
            categorySelect.clearOptions();
            categorySelect.addOption({ value: '', text: '-- Select Category --' });
            categorySelect.setValue('');

            const filtered = rawCategories.filter(cat => cat.expense_type_id == selectedTypeId);

            filtered.forEach(cat => {
                categorySelect.addOption({
                    value: cat.id,
                    text: cat.name
                });
            });

            categorySelect.refreshOptions();
        }

        // Handle default value saat edit form
        const initialType = expenseTypeEl.value;
        const initialCategory = "{{ old('expense_category', $advance->expense_category ?? '') }}";

        if (initialType) {
            updateCategoryOptions(initialType);
            categorySelect.setValue(initialCategory);
        }
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


@endpush