<form action="{{ route('admin.settlement.edit', $advance->id) }}" method="POST">

    @csrf

    <div class="row g-2">
        {{-- Kode --}}
        <div class="col-md-6">
            <label for="code_advance" class="form-label form-label-sm">Kode Advance</label>
            <input type="text" name="code_advance" id="code_advance" class="form-control form-control-sm"
                value="{{ old('code_advance', $advance->code_advance ?? $noAdvance ?? '') }}" readonly>
        </div>
        <div class="col-md-6">
            <label for="code_settlement" class="form-label form-label-sm">Kode Settlement</label>
            <input type="text" name="code_settlement" id="code_settlement" class="form-control form-control-sm"
                value="{{ old('code_settlement', $advance->code_settlement ?? $codeSettlement ?? '') }}" readonly>
        </div>

        {{-- Vendor Name --}}
        <div class="col-md-12">
            <label for="vendor_name" class="form-label form-label-sm">Vendor Name</label>
            <input type="text" name="vendor_name" id="vendor_name" class="form-control form-control-sm"
                value="{{ old('vendor_name', $advance->vendor_name ?? '') }}" required>
        </div>

        {{-- Expense --}}
        <div class="col-md-6">
            <label class="form-label form-label-sm">Expense Type</label>
            <select name="expense_type" id="expense_type" class="form-select form-select-sm" required>
                <option value="">-- Select Type --</option>
                @foreach ($expenseTypes as $type)
                    <option value="{{ $type->id }}"
                        {{ (old('expense_type', $advance->expense_type_id ?? '') == $type->id) ? 'selected' : '' }}>
                        {{ $type->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label form-label-sm">Expense Category</label>
            <select name="expense_category" id="expense_category" class="form-select form-select-sm" required {{ empty($advance) ? 'disabled' : '' }}>
                <option value="">-- Select Category --</option>
                @foreach ($expenseCategories as $cat)
                    <option value="{{ $cat->id }}"
                        data-type="{{ $cat->expense_type_id }}"
                        {{ (old('expense_category', $advance->expense_category_id ?? '') == $cat->id) ? 'selected' : '' }}>
                        {{ $cat->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Nominal --}}
        <div class="col-md-4">
            <label class="form-label form-label-sm">Nominal Advance (Rp)</label>
            <input type="text" name="nominal_advance" id="nominal_advance" class="form-control form-control-sm"
                value="{{ number_format(old('nominal_advance', $advance->nominal_advance ?? 0), 0, ',', '.') }}" readonly>
        </div>
        <div class="col-md-4">
            <label class="form-label form-label-sm">Nominal Settlement (Rp)</label>
            <input type="text" name="nominal_settlement" id="nominal_settlement" class="form-control form-control-sm"
                value="{{ number_format(old('nominal_settlement', $advance->nominal_settlement ?? 0), 0, ',', '.') }}" readonly>
        </div>
        <div class="col-md-4">
            <label class="form-label form-label-sm">Difference (Rp)</label>
            <input type="text" name="difference" id="difference" class="form-control form-control-sm"
                value="{{ number_format(old('difference', $advance->difference ?? 0), 0, ',', '.') }}" readonly>
        </div>

        {{-- Deskripsi --}}
        <div class="col-md-12">
            <label class="form-label form-label-sm">Description</label>
            <textarea name="description" rows="2" class="form-control form-control-sm" required>{{ old('description', $advance->description ?? '') }}</textarea>
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
                            <input type="text" name="items[{{ $i }}][description]" class="form-control form-control-sm"
                                value="{{ old("items.$i.description", $item['description'] ?? '') }}">
                        </td>
                        <td>
                            <input type="number" name="items[{{ $i }}][qty]" class="form-control form-control-sm qty" min="1"
                                value="{{ old("items.$i.qty", $item['qty'] ?? 1) }}">
                        </td>
                        <td>
                            <input type="number" name="items[{{ $i }}][nominal]" class="form-control form-control-sm nominal" min="0"
                                value="{{ old("items.$i.nominal", $item['nominal'] ?? 0) }}">
                        </td>
                        <td>
                            <input type="number" class="form-control form-control-sm total" 
                                value="{{ ($item['qty'] ?? 1) * ($item['nominal'] ?? 0) }}" readonly>
                        </td>
                        <td>
                            <button type="button" class="btn btn-sm btn-danger remove-item">&times;</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td>1</td>
                        <td><input type="text" name="items[0][description]" class="form-control form-control-sm"></td>
                        <td><input type="number" name="items[0][qty]" class="form-control form-control-sm qty" min="1" value="1"></td>
                        <td><input type="number" name="items[0][nominal]" class="form-control form-control-sm nominal" min="0"></td>
                        <td><input type="number" class="form-control form-control-sm total" readonly></td>
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