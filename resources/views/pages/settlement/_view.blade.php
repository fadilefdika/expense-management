<div class="px-3 pb-3">

    <div class="row gx-3 gy-3">
        <div class="col-md-4">
            <div class="label-text">Advance Code</div>
            <div class="value-text">{{ $advance->code_advance }}</div>
        </div>
        <div class="col-md-4">
            <div class="label-text">Settlement Code</div>
            <div class="value-text">{{ $advance->code_settlement ?? '-' }}</div>
        </div>
        <div class="col-md-4">
            <div class="label-text">Submitted Date</div>
            <div class="value-text">
                {{ $advance->date_advance ? \Carbon\Carbon::parse($advance->date_advance)->format('d M Y, H:i') : '-' }}
            </div>
        </div>
    
        <div class="col-md-6">
            <div class="label-text">PO / Invoice Number</div>
            <div class="value-text">{{ $advance->invoice_number ?? '-' }}</div>
        </div>
        <div class="col-md-6">
            <div class="label-text">Vendor Name</div>
            <div class="value-text">{{ $advance->vendor->name ?? '-' }}</div>
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
    
        @if ($advance->main_type === 'Advance')
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
        @else
            <div class="col-md-4">
                <div class="label-text">Nominal PR-Online</div>
                <div class="value-text" id="nominal-idr">Rp {{ number_format($advance->nominal_settlement ?? 0, 0, ',', '.') }}</div>
            </div>
            <div class="col-md-4">
                <div class="label-text">USD Equivalent</div>
                <div class="value-text" id="nominal-usd">USD {{ number_format($advance->usd_settlement ?? 0, 3, '.', '') }}</div>
            </div>
            <div class="col-md-4">
                <div class="label-text">YEN Equivalent</div>
                <div class="value-text" id="nominal-yen">¥ {{ number_format($advance->yen_settlement ?? 0, 3, '.', '') }}</div>
            </div>            
    
            {{-- Simpan nominal settlement dalam data-attribute --}}
            <div id="nominal-wrapper" data-nominal="{{ $advance->nominal_settlement ?? 0 }}"></div>
        @endif
    
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
                        <th>GL Account</th>
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
                            <td>{{ $item->ledgerAccount->desc_coa ?? '-' }}</td>
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
                            <th colspan="5" class="text-end">Total</th>
                            <th class="text-end">
                                Rp {{ number_format($advance->settlementItems->sum(fn($i) => $i['qty'] * $i['nominal']), 0, ',', '.') }}
                            </th>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>

    <div>
        <div class="mb-2 fw-semibold text-muted" style="font-size: 12px;">Cost Center</div>

        <div class="table-responsive">
            <table class="table table-sm table-bordered align-middle mb-0">
                <thead class="table-light" style="font-size: 11px;">
                    <tr>
                        <th style="width: 40px;">No</th>
                        <th>Cost Center</th>
                        <th>GL Account</th>
                        <th>Description</th>
                        <th class="text-end" style="width: 120px;">Amount</th>
                    </tr>
                </thead>

                <tbody style="font-size: 11px;">
                    @forelse($advance->costCenterItems as $i => $item)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $item['cost_center'] ?? '-' }}</td>
                            <td>{{ $item->ledgerAccount->desc_coa ?? '-' }}</td>
                            <td>{{ $item->description ?? '-' }}</td>
                            <td class="text-end">{{ number_format($item->amount, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted fst-italic py-3">
                                No cost center data available.
                            </td>
                        </tr>
                    @endforelse
                </tbody>

                @if($advance->costCenterItems->isNotEmpty())
                    <tfoot style="font-size: 11px;">
                        <tr>
                            <th colspan="4" class="text-end">Total</th>
                            <th class="text-end">
                                Rp {{ number_format($advance->nominal_settlement, 0, ',', '.') }}
                            </th>
                        </tr>
                        <tr>
                            <th colspan="4" class="text-end">USD</th>
                            <th class="text-end">
                                $ {{ number_format($advance->usd_settlement, 0, ',', '.') }}
                            </th>
                        </tr>
                        <tr>
                            <th colspan="4" class="text-end">YEN</th>
                            <th class="text-end">
                                ¥ {{ number_format($advance->yen_settlement, 0, ',', '.') }}
                            </th>
                        </tr>
                    </tfoot>
                @endif
            </table>
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

@push('scripts')
{{-- <script>
    document.addEventListener('DOMContentLoaded', async function () {
        const idrElement = document.getElementById('nominal-idr');
        const usdElement = document.getElementById('nominal-usd');
        const yenElement = document.getElementById('nominal-yen');
        const nominalWrapper = document.getElementById('nominal-wrapper');

        if (!nominalWrapper) return;

        const nominalRaw = nominalWrapper.dataset.nominal || '0';
        const nominalIDR = parseFloat(nominalRaw);

        const formatRupiah = angka => angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        const formatNumber = num => parseFloat(num).toLocaleString(undefined, {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });

        const EXCHANGE_URL = "{{ route('admin.exchange.rates') }}";

        try {
            const res = await fetch(EXCHANGE_URL);
            const json = await res.json();

            console.log("Exchange rates from backend:", json);

            const usdRate = parseFloat(json?.data?.USD);
            const yenRate = parseFloat(json?.data?.JPY);

            if (!isNaN(usdRate) && !isNaN(yenRate) && !isNaN(nominalIDR)) {
                const usd = nominalIDR * usdRate;
                const yen = nominalIDR * yenRate;

                if (idrElement) idrElement.textContent = 'Rp ' + formatRupiah(nominalIDR);
                if (usdElement) usdElement.textContent = 'USD ' + formatNumber(usd);
                if (yenElement) yenElement.textContent = '¥ ' + formatRupiah(Math.round(yen));
            } else {
                console.warn('Data kurs atau nominal tidak valid:', json);
            }

        } catch (err) {
            console.error('Gagal mengambil kurs:', err);
        }
    });
</script> --}}
@endpush
