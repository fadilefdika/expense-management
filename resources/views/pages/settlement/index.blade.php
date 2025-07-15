@extends('layouts.app')

@section('content')
    <style>
        .form-label-sm {
            font-size: 10px;
            margin-bottom: 0.2rem;
        }
        .table-sm th, .table-sm td {
            font-size: 10px;
            padding: 0.3rem;
        }
    </style>
    <div class="mb-3">
        <a href="{{ route('admin.all-report') }}" class="btn btn-sm btn-secondary">
            &larr; Back
        </a>
    </div>
    
    <div class="card shadow-sm rounded-4 border-0">
        <div class="card-header bg-white border-bottom py-2 px-4 d-flex justify-content-between align-items-center">
            <h6 class="mb-0 text-muted fw-semibold" style="font-size: 15px;">
                {{ isset($readonly) && $readonly ? 'Settlement Detail' : 'Form Settlement' }}
            </h6>
    
            @if(isset($readonly) && $readonly)
                <a href="{{ route('admin.settlement.edit', $advance->id) }}" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-pencil-square me-1"></i> Edit
                </a>
            @endif
        </div>
    
        <div class="card-body py-3 px-4">
            @if(isset($readonly) && $readonly)
                @include('pages.settlement._view', ['advance' => $advance])
            @else
                @include('pages.settlement._form', [
                    'advance' => $advance,
                    'expenseTypes' => $expenseTypes,
                    'expenseCategories' => $expenseCategories
                ])
            @endif
        </div>
    </div>
    
    

@endsection



@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const typeSelect = document.getElementById('expense_type');
        const categorySelect = document.getElementById('expense_category');

        function filterCategories() {
            const selectedTypeId = typeSelect.value;

            if (!selectedTypeId) {
                categorySelect.disabled = true;
                categorySelect.value = '';
                return;
            }

            categorySelect.disabled = false;

            Array.from(categorySelect.options).forEach(option => {
                if (!option.value) return; // skip placeholder
                const type = option.getAttribute('data-type');
                option.hidden = type !== selectedTypeId;
            });

            categorySelect.value = '';
        }

        typeSelect.addEventListener('change', filterCategories);

        if (typeSelect.value) {
            filterCategories();
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
