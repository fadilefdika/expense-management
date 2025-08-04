@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <x-report-table
        idPrefix="by-type"
        :headers="['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']"
        :rows="$report"
        :monthlyTotals="$monthlyTotals"
        :expenseTypes="$expenseTypes"
        :highchartsSeries="$highchartsSeries"
        :highchartsDrill="$highchartsDrill"
        title="Expense by Type & Category"
        label1="Expense Type"
        label2="Expense Category"
    />

    
</div>
@endsection