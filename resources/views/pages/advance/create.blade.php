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
                <div class="row g-2">
                    <div class="col-md-4">
                        <label class="form-label form-label-sm">Type<span class="text-danger"> *</span></label>
                        <select name="type_advance" class="form-select form-select-sm" required>
                            <option value="">-- Select Type --</option>
                            @foreach($typeAdvance as $t)
                                <option value="{{ $t->id }}">{{ $t->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
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

                    {{-- Expense Information --}}
                    <div class="col-md-6">
                        <label class="form-label form-label-sm">Expense Type<span class="text-danger"> *</span></label>
                        <select name="expense_type" id="expense_type" class="form-select form-select-sm" required>
                            <option value="">-- Select Type --</option>
                            @foreach ($expenseTypes as $type)
                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-6">
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

                    {{-- Amount Information --}}
                    <div class="col-md-6">
                        <label class="form-label form-label-sm">Nominal (IDR)<span class="text-danger"> *</span></label>
                        <input type="text" name="nominal_settlement" id="nominal_settlement" 
                            class="form-control form-control-sm" required readonly>
                    </div>
                    
                    <div class="col-md-3">
                        <label class="form-label form-label-sm">USD</label>
                        <input type="text" id="usd_settlement" class="form-control form-control-sm" readonly>
                    </div>
                    
                    <div class="col-md-3">
                        <label class="form-label form-label-sm">YEN</label>
                        <input type="text" id="yen_settlement" class="form-control form-control-sm" readonly>
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
                                        <th>Description</th>
                                        <th style="width: 80px;">Qty</th>
                                        <th style="width: 120px;">Nominal</th>
                                        <th style="width: 120px;">Total</th>
                                        <th style="width: 40px;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>1</td>
                                        <td><input type="text" name="items[0][description]" class="form-control form-control-sm"></td>
                                        <td><input type="number" name="items[0][qty]" class="form-control form-control-sm qty" min="1" value="1"></td>
                                        <td><input type="number" name="items[0][nominal]" class="form-control form-control-sm nominal" min="0"></td>
                                        <td><input type="text" class="form-control form-control-sm total" readonly></td>
                                        <td class="text-center"><button type="button" class="btn btn-sm btn-danger remove-item">&times;</button></td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="4" class="text-end">Total Amount</th>
                                        <th><input type="text" id="grandTotal" class="form-control form-control-sm" readonly></th>
                                        <th></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <button type="button" class="btn btn-sm btn-secondary mt-2" id="addItem">
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
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const typeSelect = document.getElementById('type_settlement');
        const vendorSelect = document.getElementById('vendor_id');
        const allVendorOptions = Array.from(vendorSelect.options);
    
        // Inisialisasi Tom Select
        const tomSelect = new TomSelect(vendorSelect, {
            placeholder: "-- Select Vendor --",
            allowEmptyOption: true,
            create: false,
            sortField: {
                field: "text",
                direction: "asc"
            }
        });
    
        // Saat type_settlement berubah
        typeSelect.addEventListener('change', function () {
            const selectedTypeId = this.value;
    
            // Reset pilihan & isi ulang
            tomSelect.clear();               // clear selected value
            tomSelect.clearOptions();        // hapus semua opsi
    
            // Filter dan tambahkan ulang opsi yang cocok
            const matchingOptions = allVendorOptions.filter(option =>
                option.value === "" || option.dataset.type === selectedTypeId
            );
    
            matchingOptions.forEach(option => {
                tomSelect.addOption({
                    value: option.value,
                    text: option.text,
                    type: option.dataset.type
                });
            });
    
            tomSelect.refreshOptions(false);     // render ulang dropdown
            tomSelect.setValue("");              // pastikan tidak ada yang terseleksi
            tomSelect.focus();                   // UX: arahkan ke input vendor
    
            // Tambahan UX: disable kalau tidak ada opsi selain placeholder
            if (matchingOptions.length <= 1) {
                tomSelect.disable();
            } else {
                tomSelect.enable();
            }
        });
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const formatNominal = (input) => {
            input.addEventListener('input', function () {
                let value = this.value.replace(/\D/g, '');
                this.value = value.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            });
        };
    
        const nominalAdvance = document.getElementById('nominal_advance');
        const nominalSettlement = document.getElementById('nominal_settlement');
    
        if (nominalAdvance) formatNominal(nominalAdvance);
        if (nominalSettlement) formatNominal(nominalSettlement);
    });
</script>
      
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const rawCategorySelect = document.getElementById('expense_category');
        const rawTypeSelect = document.getElementById('expense_type');
    
        const allCategoryOptions = Array.from(rawCategorySelect.options);
    
        // Inisialisasi Tom Select untuk Type
        const tomSelectType = new TomSelect(rawTypeSelect, {
            placeholder: "-- Select Type --",
            allowEmptyOption: true,
            create: false,
            sortField: { field: "text", direction: "asc" }
        });
    
        // Inisialisasi Tom Select untuk Category
        const tomSelectCategory = new TomSelect(rawCategorySelect, {
            placeholder: "-- Select Category --",
            allowEmptyOption: true,
            create: false,
            sortField: { field: "text", direction: "asc" }
        });
    
        // Awalnya disable category select
        tomSelectCategory.disable();
    
        function filterCategories() {
            const selectedTypeId = rawTypeSelect.value;
    
            // Reset kategori
            tomSelectCategory.clear(); // kosongkan pilihan
            tomSelectCategory.clearOptions(); // hapus opsi
    
            if (!selectedTypeId) {
                tomSelectCategory.disable();
                return;
            }
    
            // Filter dan masukkan kembali ke dropdown
            const filteredOptions = allCategoryOptions.filter(option => {
                return option.value === "" || option.dataset.type === selectedTypeId;
            });
    
            filteredOptions.forEach(option => {
                tomSelectCategory.addOption({
                    value: option.value,
                    text: option.text,
                    type: option.dataset.type
                });
            });
    
            tomSelectCategory.refreshOptions(false);
            tomSelectCategory.enable();
            tomSelectCategory.setValue("");
            tomSelectCategory.focus();
        }
    
        // Ketika expense_type berubah
        rawTypeSelect.addEventListener('change', filterCategories);
    
        // Jalankan filter saat pertama load (misal ada preset value)
        filterCategories();
    });
</script>
    
    
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const mainTypeSelect = document.getElementById('main_type');
        const advanceSection = document.getElementById('advance-section');
        const prSection = document.getElementById('pr-section');
    
        // Fungsi untuk toggle section dan disable input
        function toggleSections() {
            const advanceInputs = advanceSection.querySelectorAll('input, select, textarea');
            const prInputs = prSection.querySelectorAll('input, select, textarea');
    
            if (mainTypeSelect.value === 'advance') {
                advanceSection.classList.remove('d-none');
                prSection.classList.add('d-none');
    
                // enable advance, disable pr
                advanceInputs.forEach(i => i.disabled = false);
                prInputs.forEach(i => i.disabled = true);
            } else if (mainTypeSelect.value === 'pr_online') {
                prSection.classList.remove('d-none');
                advanceSection.classList.add('d-none');
    
                // enable pr, disable advance
                prInputs.forEach(i => i.disabled = false);
                advanceInputs.forEach(i => i.disabled = true);
            } else {
                prSection.classList.add('d-none');
                advanceSection.classList.add('d-none');
    
                // disable all
                prInputs.forEach(i => i.disabled = true);
                advanceInputs.forEach(i => i.disabled = true);
            }
        }
    
        // Panggil saat halaman siap dan saat berubah
        toggleSections();
        mainTypeSelect.addEventListener('change', toggleSections);
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const table = document.getElementById('rincianTable').getElementsByTagName('tbody')[0];
        const nominalSettlementInput = document.getElementById('nominal_settlement');
        const usdSettlementInput = document.getElementById('usd_settlement');
        const yenSettlementInput = document.getElementById('yen_settlement');
        const grandTotalInput = document.getElementById('grandTotal');
    
        let exchangeRates = {
            usd: null,
            yen: null,
        };
    
        let debounceTimer;
    
        const EXCHANGE_RATE_URL = "{{ route('admin.exchange.rates') }}";
        async function fetchExchangeRates() {
            try {
                const res = await fetch(EXCHANGE_RATE_URL);

                const json = await res.json();
                console.log(json);
                if (json?.data?.USD && json?.data?.JPY) {
                    exchangeRates.usd = parseFloat(json.data.USD);
                    exchangeRates.yen = parseFloat(json.data.JPY);
                    updateGrandTotal();
                } else {
                    console.warn('Format API tidak sesuai:', json);
                }
            } catch (err) {
                console.error('Gagal fetch kurs:', err);
            }
        }
    
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
            debounceUpdateGrandTotal();
        }
    
        function updateGrandTotal() {
            let grandTotal = 0;
            const rows = table.querySelectorAll('tr');
            rows.forEach(row => {
                const totalInput = row.querySelector('.total');
                if (totalInput) {
                    grandTotal += parseNumber(totalInput.value); // <- gunakan angka asli
                }
            });

            // Set ke input Nominal IDR (format hanya untuk tampilan)
            nominalSettlementInput.value = formatRupiah(grandTotal);
            grandTotalInput.value = formatRupiah(grandTotal);

            // Gunakan grandTotal mentah untuk konversi
            if (exchangeRates.usd && exchangeRates.yen) {
                const usd = (grandTotal * exchangeRates.usd).toFixed(2);
                const yen = (grandTotal * exchangeRates.yen).toFixed(2);

                usdSettlementInput.value = formatRupiah(usd);
                yenSettlementInput.value = formatRupiah(yen);
            }
        }


    
        function debounceUpdateGrandTotal() {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(updateGrandTotal, 500);
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
                debounceUpdateGrandTotal();
            }
        });
    
        function renumberRows() {
            const rows = table.querySelectorAll('tr');
            rows.forEach((row, index) => {
                row.querySelector('td:first-child').textContent = index + 1;
            });
        }
    
        fetchExchangeRates(); // Jalankan saat halaman dimuat
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

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const dateInputAdvance = document.getElementById('submitted_date_advance');
        const dateInputSettlement = document.getElementById('submitted_date_settlement');

        // Ambil waktu saat ini dan format jadi YYYY-MM-DDTHH:MM
        const now = new Date();
        const year = now.getFullYear();
        const month = String(now.getMonth() + 1).padStart(2, '0');
        const day = String(now.getDate()).padStart(2, '0');
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const formattedDate = `${year}-${month}-${day}T${hours}:${minutes}`;

        // Set sebagai nilai default
        dateInputAdvance.value = formattedDate;
        dateInputSettlement.value = formattedDate;
    });
</script>

@endpush