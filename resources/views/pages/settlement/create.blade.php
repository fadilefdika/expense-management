@extends('layouts.app')

@section('content')
<style>
    .form-label-sm {
        font-size: 0.75rem;
        margin-bottom: 0.2rem;
    }
</style>

<div class="card">
    <div class="card-header py-2"><h6 class="mb-0">Form Settlement</h6></div>
    <div class="card-body py-2">
        <form action="#{{-- route('settlement.store') --}}" method="POST">
            @csrf

            <div class="row g-2">
                {{-- Kode --}}
                <div class="col-md-6">
                    <label for="code_advance" class="form-label form-label-sm">Kode Advance</label>
                    <input type="text" name="code_advance" id="code_advance" class="form-control form-control-sm"
                           value="{{ $noAdvance }}" readonly>
                </div>
                <div class="col-md-6">
                    <label for="code_settlement" class="form-label form-label-sm">Kode Settlement</label>
                    <input type="text" name="code_settlement" id="code_settlement" class="form-control form-control-sm" value="{{$codeSettlement}}" readonly>
                </div>

                {{-- Vendor Name --}}
                <div class="col-md-12">
                    <label for="vendor_name" class="form-label form-label-sm">Vendor Name</label>
                    <input type="text" name="vendor_name" id="vendor_name" class="form-control form-control-sm" required>
                </div>

                {{-- Expense Type & Category --}}
                <div class="col-md-6">
                    <label class="form-label form-label-sm">Expense Type</label>
                    <select name="expense_type" id="expense_type" class="form-select form-select-sm" required>
                        <option value="">-- Pilih Type --</option>
                        @foreach ($expenseTypes as $type)
                            <option value="{{ $type->id }}" {{ $advance->expense_type_id == $type->id ? 'selected' : '' }}>
                                {{ $type->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label form-label-sm">Expense Category</label>
                    <select name="expense_category" id="expense_category" class="form-select form-select-sm" required disabled>
                        <option value="">-- Pilih Category --</option>
                        @foreach ($expenseCategories as $cat)
                            <option value="{{ $cat->id }}"
                                    data-type="{{ $cat->expense_type_id }}"
                                    {{ $advance->expense_category_id == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Nominal --}}
                <div class="col-md-6">
                    <label for="nominal_advance" class="form-label form-label-sm">Nominal Advance (Rp)</label>
                    <input type="number" name="nominal_advance" id="nominal_advance" class="form-control form-control-sm"
                           value="{{ old('nominal_advance', $advance->nominal ?? '') }}" readonly>
                </div>
                <div class="col-md-6">
                    <label for="nominal_settlement" class="form-label form-label-sm">Nominal Settlement (Rp)</label>
                    <input type="number" name="nominal_settlement" id="nominal_settlement" class="form-control form-control-sm"
                           value="{{ old('nominal_settlement') }}" required>
                </div>

                <div class="col-md-6">
                    <label for="nominal_settlement" class="form-label form-label-sm">Different (Rp)</label>
                    <input type="number" name="nominal_settlement" id="nominal_settlement" class="form-control form-control-sm"
                           value="{{ old('nominal_settlement') }}" required>
                </div>

                {{-- Deskripsi --}}
                <div class="col-md-12">
                    <label for="description" class="form-label form-label-sm">Deskripsi</label>
                    <textarea name="description" id="description" class="form-control form-control-sm" rows="2" required>{{ old('description', $advance->description ?? '') }}</textarea>
                </div>

                {{-- Submit --}}
                <div class="col-12 mt-2">
                    <button type="submit" class="btn btn-sm btn-primary">Submit Settlement</button>
                </div>
            </div>
        </form>
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
@endpush
