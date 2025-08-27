<form action="{{ route('admin.settlement.update', $advance->id) }}" method="POST">

    @csrf

    <div class="row g-3">

        {{-- Kode --}}
        <div class="col-md-6">
            <label for="code_advance_edit" class="form-label form-label-sm fw-bold">Advance Code</label>
            <input type="text" name="code_advance_edit" id="code_advance_edit"
                class="form-control form-control-sm shadow-sm"
                style="font-size: 11px;"
                value="{{ old('code_advance', $advance->code_advance ?? $noAdvance ?? '') }}" readonly>
        </div>
    
        <div class="col-md-6">
            <label for="code_settlement_edit" class="form-label form-label-sm fw-bold">Settlement Code</label>
            <input type="text" name="code_settlement_edit" id="code_settlement_edit"
                class="form-control form-control-sm shadow-sm"
                style="font-size: 11px;"
                value="{{ old('code_settlement', $advance->code_settlement ?? $codeSettlement ?? '') }}" readonly>
        </div>
    
        {{-- Vendor Name --}}
        <div class="col-md-6">
            <label for="vendor_id_edit" class="form-label form-label-sm fw-bold">Vendor Name</label>
            <select name="vendor_id_edit" id="vendor_id_edit"
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
            <input type="number" name="invoice_number_edit" id="invoice_number_edit" class="form-control form-control-sm" placeholder="Optional" value="{{ old('invoice_number', $advance->invoice_number ?? '') }}">
        </div>
    
        {{-- Expense Type --}}
        <div class="col-md-6">
            <label for="expense_type_edit" class="form-label form-label-sm fw-bold">Expense Type</label>
            <select name="expense_type_edit" id="expense_type_edit"
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
            <label for="expense_category_edit" class="form-label form-label-sm fw-bold">Expense Category</label>
            <select name="expense_category_edit" id="expense_category_edit"
                class="form-select form-select-sm shadow-sm"
                style="font-size: 11px;" required>
                <option value="">-- Select Category --</option>
                @foreach ($expenseCategories as $category)
                    <option value="{{ $category->id }}"
                        data-type="{{ $category->expense_type_id }}"
                        @selected(old('expense_category', $advance->expense_category ?? '') == $category->id)>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>
    
        {{-- Nominal --}}
        <div class="col-md-4">
            <label for="nominal_advance_edit" class="form-label form-label-sm fw-bold">Nominal Advance (Rp)</label>
            <input type="text" name="nominal_advance_edit" id="nominal_advance_edit"
                class="form-control form-control-sm shadow-sm"
                style="font-size: 11px;"
                value="{{ number_format(old('nominal_advance', $advance->nominal_advance ?? 0), 0, ',', '.') }}" readonly>
        </div>
    
        <div class="col-md-4">
            <label for="nominal_settlement_edit" class="form-label form-label-sm fw-bold">Nominal Settlement (Rp)</label>
            <input type="text" name="nominal_settlement_edit" id="nominal_settlement_edit"
                class="form-control form-control-sm shadow-sm"
                style="font-size: 11px;"
                value="{{ number_format(old('nominal_settlement', $advance->nominal_settlement ?? 0), 0, ',', '.') }}" readonly>
        </div>
    
        <div class="col-md-4">
            <label for="difference_edit" class="form-label form-label-sm fw-bold">Difference (Rp)</label>
            <input type="text" name="difference_edit" id="difference_edit"
                class="form-control form-control-sm shadow-sm"
                style="font-size: 11px;"
                value="{{ number_format(old('difference', $advance->difference ?? 0), 0, ',', '.') }}" readonly>
        </div>
    
        {{-- Deskripsi --}}
        <div class="col-md-6">
            <label for="description_edit" class="form-label form-label-sm fw-bold">Description</label>
            <textarea name="description_edit" id="description_edit" rows="2"
                class="form-control form-control-sm shadow-sm"
                style="font-size: 11px;" required>{{ old('description', $advance->description ?? '') }}</textarea>
        </div>
    
    </div>
  

    {{-- Usage Details Table --}}
    <div class="col-12 mt-2">
        <label class="form-label form-label-sm fw-bold">Usage Details</label>
        <div class="table-responsive">
            <table class="table table-bordered table-sm" id="rincianTableEdit">
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
                    @php
                        $usageItems = old('usage_items', $advance->settlementItems->toArray() ?? []);
                    @endphp
                
                    @foreach ($usageItems as $i => $item)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>
                                <select class="form-select form-select-sm ledger-account-select-usage-details"
                                        name="usage_items[{{ $i }}][ledger_account_id]">
                            
                                    @php
                                        $selectedLedgerId = $item['ledger_account_id']
                                            ?? $item['ledger_account']
                                            ?? ($item['ledgerAccount']['id'] ?? null);
                                    @endphp
                            
                                    {{-- tampilkan opsi default hanya kalau belum ada ledger yg dipilih --}}
                                    @if(!$selectedLedgerId)
                                        <option value="">-- Select GL Account --</option>
                                    @endif
                            
                                    @foreach ($ledgerAccounts as $ledger)
                                        <option value="{{ $ledger->id }}" @selected($selectedLedgerId == $ledger->id)>
                                            {{ $ledger->ledger_account }} - {{ $ledger->desc_coa }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>                                                       
                            <td>
                                <input type="text" name="usage_items[{{ $i }}][description]"
                                       class="form-control form-control-sm"
                                       value="{{ $item['description'] ?? '' }}" required>
                            </td>
                            <td>
                                <input type="number" name="usage_items[{{ $i }}][qty]"
                                       class="form-control form-control-sm qty"
                                       min="1" value="{{ $item['qty'] ?? 1 }}">
                            </td>
                            <td>
                                <input type="number" name="usage_items[{{ $i }}][nominal]"
                                       class="form-control form-control-sm nominal"
                                       min="0" value="{{ $item['nominal'] ?? 0 }}">
                            </td>
                            <td>
                                <input type="text" class="form-control form-control-sm total"
                                       readonly
                                       value="{{ number_format(($item['qty'] ?? 0) * ($item['nominal'] ?? 0)) }}">
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-danger remove-item">&times;</button>
                            </td>
                        </tr>
                    @endforeach
                
                    {{-- Kalau kosong, tampilkan baris default --}}
                    @if (count($usageItems) === 0)
                        <tr>
                            <td>1</td>
                            <td>
                                <select class="form-select form-select-sm ledger-account-select-usage-details"
                                        name="usage_items[0][ledger_account_id]">
                                    <option value="">-- Select GL Account --</option>
                                    @foreach ($ledgerAccounts as $ledger)
                                        <option value="{{ $ledger->id }}">
                                            {{ $ledger->ledger_account }} - {{ $ledger->desc_coa }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td><input type="text" name="usage_items[0][description]" class="form-control form-control-sm" required></td>
                            <td><input type="number" name="usage_items[0][qty]" class="form-control form-control-sm qty" min="1" value="1"></td>
                            <td><input type="number" name="usage_items[0][nominal]" class="form-control form-control-sm nominal" min="0"></td>
                            <td><input type="text" class="form-control form-control-sm total" readonly></td>
                            <td class="text-center"><button type="button" class="btn btn-sm btn-danger remove-item">&times;</button></td>
                        </tr>
                    @endif
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="5" class="text-end">Total Amount</th>
                        <th><input type="text" id="grandTotalUsageDetailsEdit" class="form-control form-control-sm" readonly></th>
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
            <table class="table table-bordered table-sm" id="costCenterTableEdit">
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
                        <th><input type="text" id="grandTotalCostCenterEdit" class="form-control form-control-sm" readonly></th>
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
                <input type="hidden" name="grand_total_cost_center_edit" id="grand_total_cost_center_edit">
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
    @vite('resources/js/form.js')
@endpush