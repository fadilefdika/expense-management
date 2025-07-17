@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <x-report-table
        :headers="['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']"
        :rows="$report"
        :monthlyTotals="$monthlyTotals"
        title="Expense by Type & Category"
        label1="Expense Type"
        label2="Expense Category"
    />

    <x-report-table
        :headers="['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']"
        :rows="$vendorReport"
        :monthlyTotals="$vendorTotals"
        title="Expense by Vendor"
        label1="Vendor"
    />
    
</div>
@endsection
