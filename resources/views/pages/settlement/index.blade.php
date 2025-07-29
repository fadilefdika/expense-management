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
                @php
                    $text = '';
            
                    if (isset($readonly) && $readonly) {
                        $text = $advance->main_type == 'Advance' ? 'Settlement Detail' : 'PR-Online';
                    } else {
                        $text = $advance->main_type == 'Advance' ? 'Form Settlement' : 'Form PR-Online';
                    }
                @endphp
            
                {{ $text }}
            </h6>
            
    
            @if(isset($readonly) && $readonly)
                <a href="{{ route('admin.settlement.edit', $advance->id) }}" class="btn btn-sm btn-outline-primary">
                Edit
                </a>
            @else
            <a href="{{ route('admin.settlement.show', $advance->id) }}" class="btn btn-sm btn-outline-primary">
                Cancel
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
                    'expenseCategories' => $expenseCategories,
                    'vendors' => $vendors,
                ])
            @endif
        </div>
    </div>
    
    

@endsection




