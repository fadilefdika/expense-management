<div class="px-3 pb-3">
    <div class="row gx-3 gy-2">
        <div class="col-md-6">
            <div class="label-text">Advance Code</div>
            <div class="value-text">{{ $advance->code_advance }}</div>
        </div>
        <div class="col-md-6">
            <div class="label-text">Settlement Code</div>
            <div class="value-text">{{ $codeSettlement ?? '-' }}</div>
        </div>

        <div class="col-md-12">
            <div class="label-text">Vendor Name</div>
            <div class="value-text">{{ $advance->vendor_name ?? '-' }}</div>
        </div>

        <div class="col-md-6">
            <div class="label-text">Expense Type</div>
            <div class="value-text">
                {{ optional($expenseTypes->firstWhere('id', $advance->expense_type))->name ?? '-' }}
            </div>
        </div>
        <div class="col-md-6">
            <div class="label-text">Expense Category</div>
            <div class="value-text">
                {{ optional($expenseCategories->firstWhere('id', $advance->expense_category))->name ?? '-' }}
            </div>
        </div>        

        <div class="col-md-4">
            <div class="label-text">Nominal Advance</div>
            <div class="value-text">Rp {{ number_format($advance->nominal_advance ?? 0, 0, ',', '.') }}</div>
        </div>
        <div class="col-md-4">
            <div class="label-text">Nominal Settlement</div>
            <div class="value-text">Rp {{ number_format($advance->nominal_settlement ?? 0, 0, ',', '.') }}</div>
        </div>
        <div class="col-md-4">
            <div class="label-text">Difference</div>
            <div class="value-text">Rp {{ number_format($advance->difference ?? 0, 0, ',', '.') }}</div>
        </div>

        <div class="col-12">
            <div class="label-text">Description</div>
            <div class="value-text" style="white-space: pre-line;">{{ $advance->description }}</div>
        </div>
    </div>

    <hr class="my-4">

    <div>
        <div class="mb-2 fw-semibold text-muted" style="font-size: 12px;">Usage Details</div>
    
        <div class="table-responsive">
            <table class="table table-sm table-bordered align-middle mb-0">
                <thead class="table-light" style="font-size: 11px;">
                    <tr>
                        <th style="width: 40px;">No</th>
                        <th>Description</th>
                        <th class="text-center" style="width: 80px;">Qty</th>
                        <th class="text-end" style="width: 120px;">Nominal (Rp)</th>
                        <th class="text-end" style="width: 120px;">Total (Rp)</th>
                    </tr>
                </thead>
    
                <tbody style="font-size: 11px;">
                    @forelse($advance->settlementItems as $i => $item)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $item['description'] }}</td>
                            <td class="text-center">{{ $item['qty'] }}</td>
                            <td class="text-end">{{ number_format($item['nominal'], 0, ',', '.') }}</td>
                            <td class="text-end">{{ number_format($item['qty'] * $item['nominal'], 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted fst-italic py-3">
                                No usage data available.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
    
                @if($advance->settlementItems->isNotEmpty())
                    <tfoot style="font-size: 11px;">
                        <tr>
                            <th colspan="4" class="text-end">Total</th>
                            <th class="text-end">
                                Rp {{ number_format($advance->settlementItems->sum(fn($i) => $i['qty'] * $i['nominal']), 0, ',', '.') }}
                            </th>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>
    
</div>


<style>
    .label-text {
        font-size: 12px;
        color: #6c757d;
        margin-bottom: 2px;
    }

    .value-text {
        font-size: 11px;
        font-weight: 600;
        color: #212529;
        line-height: 1.4;
        padding: 4px 0;
    }

    .table-sm th,
    .table-sm td {
        font-size: 11px;
    }

    .table th,
    .table td {
        vertical-align: middle;
    }
</style>

    