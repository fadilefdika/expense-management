@extends('layouts.app')

@push('styles')
<style>
    /* Hilangkan panah input number */
    input[type=number]::-webkit-inner-spin-button,
    input[type=number]::-webkit-outer-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    input[type=number] {
        -moz-appearance: textfield;
    }
</style>
<style>
    .card-body {
        font-size: 12px;
    }

    .form-label-sm {
        margin-bottom: 2px;
        font-weight: 500;
    }

    .form-control-sm,
    .form-select-sm {
        font-size: 12px;
        padding: 4px 6px;
    }

    #rincianTable th,
    #rincianTable td {
        padding: 3px;
        font-size: 11px;
    }

    textarea.form-control-sm {
        height: 60px;
        resize: vertical;
    }

    .btn-sm {
        padding: 3px 8px;
        font-size: 11px;
    }

    .table > :not(caption) > * > * {
        vertical-align: middle;
    }
</style>

@endpush

@section('content')
<div class="mb-3 me-3 d-flex justify-content-end">
    <a href="{{ route('admin.all-report') }}" class="btn btn-sm btn-secondary">
        Back
    </a>
</div>

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
                    <label for="main_type" class="form-label form-label-sm">Main Type<span class="text-danger"> *</span></label>
                    <select name="main_type" id="main_type" class="form-select form-select-sm" required>
                        <option value="">-- Select Main Type --</option>
                        <option value="advance">Advance</option>
                        <option value="pr_online">PR Online</option>
                    </select>
                </div>
            </div>

            {{-- SECTION: ADVANCE --}}
            <div id="advance-section" class="mt-4 d-none">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label form-label-sm">Type<span class="text-danger"> *</span></label>
                        <select name="type_advance" class="form-select form-select-sm" required>
                            <option value="">-- Select Type --</option>
                            @foreach($typeAdvance as $t)
                                <option value="{{ $t->id }}">{{ $t->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label form-label-sm">PO / Invoice Number</label>
                        <input type="number" name="invoice_number" id="invoice_number" class="form-control form-control-sm" placeholder="Optional">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label form-label-sm">Submitted Date<span class="text-danger"> *</span></label>
                        <input type="datetime-local" name="submitted_date_advance" id="submitted_date_advance" class="form-control form-control-sm" required>
                    </div>                                      
                    <div class="col-md-6">
                        <label class="form-label form-label-sm">Nominal<span class="text-danger"> *</span></label>
                        <input type="text" name="nominal_advance" id="nominal_advance" class="form-control form-control-sm" required>
                    </div>                    
                    <div class="col-md-12">
                        <label class="form-label form-label-sm">Description<span class="text-danger"> *</span></label>
                        <textarea name="description" rows="2" class="form-control form-control-sm" required></textarea>
                    </div>
                </div>
            </div>

            {{-- SECTION: PR ONLINE --}}
            <div id="pr-section" class="mt-4 d-none">
                <div class="row g-3">
                    {{-- Basic Information --}}
                    <div class="col-md-4">
                        <label class="form-label form-label-sm">Type<span class="text-danger"> *</span></label>
                        <select name="type_settlement" id="type_settlement" class="form-select form-select-sm" required>
                            <option value="">-- Select Type --</option>
                            @foreach($typePRO as $t)
                                <option value="{{ $t->id }}">{{ $t->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-4">
                        <label class="form-label form-label-sm">Submitted Date<span class="text-danger"> *</span></label>
                        <input type="datetime-local" name="submitted_date_settlement" id="submitted_date_settlement" 
                            class="form-control form-control-sm" required>
                    </div>
                    
                    <div class="col-md-4">
                        <label class="form-label form-label-sm">Vendor Name<span class="text-danger"> *</span></label>
                        <select name="vendor_id" id="vendor_id" class="form-select form-select-sm" required>
                            <option value="">-- Select Vendor --</option>
                            @foreach($vendors as $t)
                                <option value="{{ $t->id }}" data-type="{{ $t->em_type_id }}">{{ $t->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- PO / Invoice Number --}}
                    <div class="col-md-4">
                        <label class="form-label form-label-sm">PO / Invoice Number<span class="text-danger"> *</span></label>
                        <input type="number" name="invoice_number" id="invoice_number" class="form-control form-control-sm" required>
                    </div>

                    {{-- Expense Information --}}
                    <div class="col-md-4">
                        <label class="form-label form-label-sm">Expense Type<span class="text-danger"> *</span></label>
                        <select name="expense_type" id="expense_type" class="form-select form-select-sm" required>
                            <option value="">-- Select Type --</option>
                            @foreach ($expenseTypes as $type)
                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-4">
                        <label class="form-label form-label-sm">Expense Category<span class="text-danger"> *</span></label>
                        <select name="expense_category" id="expense_category" class="form-select form-select-sm" required disabled>
                            <option value="">-- Select Category --</option>
                            @foreach ($expenseCategories as $cat)
                                <option value="{{ $cat->id }}" data-type="{{ $cat->expense_type_id }}">
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-4">
                        <label class="form-label form-label-sm">USD today<span class="text-danger"> *</span></label>
                        <input type="number" id="usd_rate" class="form-control form-control-sm" step="0.0001" required>
                    </div>
                    
                    <div class="col-md-4">
                        <label class="form-label form-label-sm">YEN today<span class="text-danger"> *</span></label>
                        <input type="number" id="yen_rate" class="form-control form-control-sm" step="0.0001" required>
                    </div>        

                    {{-- Amount Information --}}
                    <div class="col-md-4 d-none">
                        <label class="form-label form-label-sm">Nominal (IDR)<span class="text-danger"> *</span></label>
                        <input type="text" name="nominal_settlement" id="nominal_settlement" 
                            class="form-control form-control-sm" required readonly>
                    </div>
                    
                    {{-- Description --}}
                    <div class="col-12">
                        <label class="form-label form-label-sm">Description<span class="text-danger"> *</span></label>
                        <textarea name="description" rows="2" class="form-control form-control-sm" required></textarea>
                    </div>

                    {{-- Usage Details Table --}}
                    <div class="col-12 mt-2">
                        <label class="form-label form-label-sm">Usage Details</label>
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
                        <label class="form-label form-label-sm">Cost Center</label>
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
    @vite('resources/js/create.js')

    <script>
        const parseNumberCurrency = (val) => {
            if (!val) return 0;
            return parseFloat(val.toString().replace(/\./g, '').replace(/,/g, '').replace(/[^0-9.-]+/g, '')) || 0;
        };


        const formatNumberCurrency = (num) => {
            return new Intl.NumberFormat('id-ID', { maximumFractionDigits: 2 }).format(num);
        };
        
        const updateConvertedCurrencyTotals = () => {
            const grandTotal = parseNumberCurrency(document.getElementById("grandTotalCostCenter").value || "0");
            const usdRate = parseFloat(document.getElementById("usd_rate").value || "0");
            const yenRate = parseFloat(document.getElementById("yen_rate").value || "0");

            console.log(grandTotal, usdRate, yenRate);
            const usdTotal = usdRate ? (grandTotal / usdRate) : 0;
            const yenTotal = yenRate ? (grandTotal / yenRate) : 0;

            // Update tampilan
            document.getElementById("usd_total").value = formatNumberCurrency(usdTotal);
            document.getElementById("yen_total").value = formatNumberCurrency(yenTotal);

            // Simpan ke hidden input agar terkirim ke controller
            document.getElementById("usd_settlement").value = usdTotal.toFixed(4);
            document.getElementById("yen_settlement").value = yenTotal.toFixed(4);

        };

        const updateCostCenterGrandTotal = () => {
            let total = 0;
            const totalUsage = parseNumber(
                document.getElementById("grandTotalUsageDetails")?.value || "0"
            );

            costCenterTableBody.querySelectorAll("tr").forEach((row) => {
                total += parseNumber(row.querySelector(".total")?.value || "0");
            });

            total += totalUsage;
            costCenterGrandTotalInput.value = formatRupiah(total);

            updateConvertedCurrencyTotals();
        };

        // Event listeners
        document.getElementById("grandTotalCostCenter").addEventListener("input", updateConvertedCurrencyTotals);
        document.getElementById("usd_rate").addEventListener("input", updateConvertedCurrencyTotals);
        document.getElementById("yen_rate").addEventListener("input", updateConvertedCurrencyTotals);

    </script>
@endpush