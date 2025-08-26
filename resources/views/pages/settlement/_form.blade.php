<form action="{{ route('admin.settlement.update', $advance->id) }}" method="POST">

    @csrf

    <div class="row g-3">

        {{-- Kode --}}
        <div class="col-md-6">
            <label for="code_advance" class="form-label form-label-sm fw-bold">Advance Code</label>
            <input type="text" name="code_advance" id="code_advance"
                class="form-control form-control-sm shadow-sm"
                style="font-size: 11px;"
                value="{{ old('code_advance', $advance->code_advance ?? $noAdvance ?? '') }}" readonly>
        </div>
    
        <div class="col-md-6">
            <label for="code_settlement" class="form-label form-label-sm fw-bold">Settlement Code</label>
            <input type="text" name="code_settlement" id="code_settlement"
                class="form-control form-control-sm shadow-sm"
                style="font-size: 11px;"
                value="{{ old('code_settlement', $advance->code_settlement ?? $codeSettlement ?? '') }}" readonly>
        </div>
    
        {{-- Vendor Name --}}
        <div class="col-md-6">
            <label for="vendor_id" class="form-label form-label-sm fw-bold">Vendor Name</label>
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
            <label class="form-label form-label-sm fw-bold">PO / Invoice Number</label>
            <input type="number" name="invoice_number" id="invoice_number" class="form-control form-control-sm" placeholder="Optional" value="{{ old('invoice_number', $advance->invoice_number ?? '') }}">
        </div>
    
        {{-- Expense Type --}}
        <div class="col-md-6">
            <label for="expense_type" class="form-label form-label-sm fw-bold">Expense Type</label>
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
            <label for="expense_category" class="form-label form-label-sm fw-bold">Expense Category</label>
            <select name="expense_category" id="expense_category"
                class="form-select form-select-sm shadow-sm"
                style="font-size: 11px;" required>
                <option value="">-- Select Category --</option>
            </select>
        </div>
    
        {{-- Nominal --}}
        <div class="col-md-4">
            <label for="nominal_advance" class="form-label form-label-sm fw-bold">Nominal Advance (Rp)</label>
            <input type="text" name="nominal_advance" id="nominal_advance"
                class="form-control form-control-sm shadow-sm"
                style="font-size: 11px;"
                value="{{ number_format(old('nominal_advance', $advance->nominal_advance ?? 0), 0, ',', '.') }}" readonly>
        </div>
    
        <div class="col-md-4">
            <label for="nominal_settlement" class="form-label form-label-sm fw-bold">Nominal Settlement (Rp)</label>
            <input type="text" name="nominal_settlement" id="nominal_settlement"
                class="form-control form-control-sm shadow-sm"
                style="font-size: 11px;"
                value="{{ number_format(old('nominal_settlement', $advance->nominal_settlement ?? 0), 0, ',', '.') }}" readonly>
        </div>
    
        <div class="col-md-4">
            <label for="difference" class="form-label form-label-sm fw-bold">Difference (Rp)</label>
            <input type="text" name="difference" id="difference"
                class="form-control form-control-sm shadow-sm"
                style="font-size: 11px;"
                value="{{ number_format(old('difference', $advance->difference ?? 0), 0, ',', '.') }}" readonly>
        </div>
    
        {{-- Deskripsi --}}
        <div class="col-md-12">
            <label for="description" class="form-label form-label-sm fw-bold">Description</label>
            <textarea name="description" id="description" rows="2"
                class="form-control form-control-sm shadow-sm"
                style="font-size: 11px;" required>{{ old('description', $advance->description ?? '') }}</textarea>
        </div>
    
    </div>
    
    
    {{-- Usage Details Table --}}
    <div class="col-12 mt-2">
        <label class="form-label form-label-sm fw-bold">Usage Details</label>
        <div class="table-responsive">
            <table class="table table-bordered table-sm" id="rincianTable">
                <thead class="table-light" style="font-size: 12px;">
                    <tr>
                        <th style="width: 40px;">No</th>
                        <th>GL Account</th>
                        <th>Description</th>
                        <th style="width: 80px;">Qty</th>
                        <th style="width: 120px;">Nominal</th>
                        <th style="width: 120px;">Amount</th>
                        <th style="width: 40px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>
                            <select class="form-select form-select-sm ledger-account-select-usage-details" name="usage_items[0][ledger_account_id]">
                                <option value="">-- Select GL Account --</option>
                            </select>
                        </td>
                        <td><input type="text" name="usage_items[0][description]" class="form-control form-control-sm" required></td>
                        <td><input type="number" name="usage_items[0][qty]" class="form-control form-control-sm qty" min="1" value="1"></td>
                        <td><input type="number" name="usage_items[0][nominal]" class="form-control form-control-sm nominal" min="0"></td>
                        <td><input type="text" class="form-control form-control-sm total" readonly></td> 
                        <td class="text-center"><button type="button" class="btn btn-sm btn-danger remove-item">&times;</button></td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="5" class="text-end">Total Amount</th>
                        <th><input type="text" id="grandTotalUsageDetails" class="form-control form-control-sm" readonly></th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>
        </div>
        <button type="button" class="btn btn-sm btn-secondary mt-2" id="addItemUsageDetails">
            <i class="fas fa-plus me-1"></i> Add Item
        </button>
    </div>


    {{-- Cost Center Table --}}
    <div class="col-12 mt-2">
        <label class="form-label form-label-sm fw-bold">Cost Center</label>
        <div class="table-responsive">
            <table class="table table-bordered table-sm" id="costCenterTable">
                <thead class="table-light" style="font-size: 12px;">
                    <tr>
                        <th style="width: 40px;">No</th>
                        <th>Cost Center</th>
                        <th>GL Account</th>
                        <th>Description</th>
                        <th style="width: 120px;">Amount</th>
                        <th style="width: 40px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td><input
                            type="text"
                            name="items_costcenter[0][cost_center]"
                            class="form-control form-control-sm"
                          /></td>
                          <td>
                            <select class="form-select form-select-sm ledger-account-select-cost-center" name="items_costcenter[0][ledger_account_id]">
                                <option value="">-- Select GL Account --</option>
                            </select>
                        </td>
                        <td><input type="text" name="items_costcenter[0][description]" class="form-control form-control-sm" required></td>
                        <td><input type="text" class="form-control form-control-sm total" name="items_costcenter[0][amount]" readonly></td>
                        <td class="text-center"><button type="button" class="btn btn-sm btn-danger remove-item">&times;</button></td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="4" class="text-end">Total Amount</th>
                        <th><input type="text" id="grandTotalCostCenter" class="form-control form-control-sm" readonly></th>
                        <th></th>
                    </tr>
                    <tr>
                        <th colspan="4" class="text-end">USD</th>
                        <th><input type="text" id="usd_total" class="form-control form-control-sm" readonly></th>
                        <th></th>
                    </tr>
                    <tr>
                        <th colspan="4" class="text-end">Yen</th>
                        <th><input type="text" id="yen_total" class="form-control form-control-sm" readonly></th>
                        <th></th>
                    </tr>
                </tfoot>
                <!-- INI YANG DIKIRIM KE CONTROLLER -->
                <input type="hidden" name="grand_total_cost_center" id="grand_total_cost_center">
                <input type="hidden" name="usd_settlement" id="usd_settlement">
                <input type="hidden" name="yen_settlement" id="yen_settlement">
            </table>
        </div>
        <button type="button" class="btn btn-sm btn-secondary mt-2" id="addItemCostCenter">
            <i class="fas fa-plus me-1"></i> Add Item
        </button>
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