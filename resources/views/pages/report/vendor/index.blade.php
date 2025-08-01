@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <x-report-table
    idPrefix="by-vendor"
    :headers="['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']"
    :rows="$vendorReport"
    :monthlyTotals="$vendorTotals"
    title="Expense by Vendor"
    label1="Vendor"
    />
    
</div>
@endsection
