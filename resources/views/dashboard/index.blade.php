@extends('layouts.app')

@section('title', 'Dashboard')

@php
    $accountContext = app(\App\Services\AccountContext::class);
    $account = $accountContext->account();
    $currencyCode = $account?->currency_code ?? 'IQD';
@endphp

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Dashboard</h4>
        </div>
        <p class="text-muted mb-4">Overview of your financial activity this month. Track your income, expenses, and balances at a glance.</p>
    </div>
</div>

<div class="row">
    <div class="col-xl-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <span class="text-muted text-uppercase font-size-12 fw-semibold">Income</span>
                        <h4 class="mb-0 text-success">{{ \App\Helpers\CurrencyHelper::format($summary['income'], $currencyCode) }}</h4>
                    </div>
                    <div class="flex-shrink-0 align-self-center">
                        <i data-feather="trending-up" class="icon-lg text-success"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <span class="text-muted text-uppercase font-size-12 fw-semibold">Expense</span>
                        <h4 class="mb-0 text-danger">{{ \App\Helpers\CurrencyHelper::format($summary['expense'], $currencyCode) }}</h4>
                    </div>
                    <div class="flex-shrink-0 align-self-center">
                        <i data-feather="trending-down" class="icon-lg text-danger"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <span class="text-muted text-uppercase font-size-12 fw-semibold">Net Balance</span>
                        <h4 class="mb-0 {{ $summary['net'] >= 0 ? 'text-primary' : 'text-danger' }}">
                            {{ number_format($summary['net'], 2) }} {{ $currencyCode }}
                        </h4>
                    </div>
                    <div class="flex-shrink-0 align-self-center">
                        <i data-feather="dollar-sign" class="icon-lg text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <span class="text-muted text-uppercase font-size-12 fw-semibold">Income Sources</span>
                        <h4 class="mb-0 text-info">{{ count($walletBalances ?? []) }}</h4>
                    </div>
                    <div class="flex-shrink-0 align-self-center">
                        <i data-feather="trending-up" class="icon-lg text-info"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-6">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-4">Top Expense Types (This Month)</h4>
                @if(!empty($topCategories) && count($topCategories) > 0)
                    <div class="table-responsive">
                        <table class="table table-nowrap mb-0">
                            <thead>
                                <tr>
                                    <th>Expense Type</th>
                                    <th class="text-end">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topCategories as $category)
                                    <tr>
                                        <td>{{ $category['category'] }}</td>
                                        <td class="text-end">
                                            <strong>{{ \App\Helpers\CurrencyHelper::format($category['total'], $currencyCode) }}</strong>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted mb-0">No expenses recorded this month</p>
                @endif
            </div>
        </div>
    </div>
    <div class="col-xl-6">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-4">Income Source Balances</h4>
                @if(!empty($walletBalances) && count($walletBalances) > 0)
                    <div class="table-responsive">
                        <table class="table table-nowrap mb-0">
                            <thead>
                                <tr>
                                    <th>Income Source</th>
                                    <th class="text-end">Balance</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($walletBalances as $wallet)
                                    <tr>
                                        <td>{{ $wallet['wallet'] }}</td>
                                        <td class="text-end">
                                            <strong class="{{ $wallet['balance'] >= 0 ? 'text-success' : 'text-danger' }}">
                                                {{ \App\Helpers\CurrencyHelper::format($wallet['balance'], $currencyCode) }}
                                            </strong>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted mb-0">
                        No income sources created yet.
                        @php
                            $accountContext = app(\App\Services\AccountContext::class);
                            $accountId = $accountContext->id();
                            $user = auth()->user();
                        @endphp
                        @if($accountId && $user->hasPermissionInAccount($accountId, 'wallets.create'))
                            <a href="{{ route('wallets.create') }}">Create one</a>
                        @endif
                    </p>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Income vs Expense Ratio</h4>
            </div>
            <div class="card-body">
                <div id="income-expense-donut-chart" style="height: 350px;"></div>
                <div class="text-center mt-3">
                    <p class="mb-1">
                        <span class="badge bg-success me-2">Income</span>
                        <strong>{{ \App\Helpers\CurrencyHelper::format($summary['income'], $currencyCode) }}</strong>
                    </p>
                    <p class="mb-1">
                        <span class="badge bg-danger me-2">Expense</span>
                        <strong>{{ \App\Helpers\CurrencyHelper::format($summary['expense'], $currencyCode) }}</strong>
                    </p>
                    @php
                        $expensePercentage = $summary['income'] > 0 ? ($summary['expense'] / $summary['income']) * 100 : 0;
                        $remainingPercentage = 100 - $expensePercentage;
                    @endphp
                    <p class="mt-2 mb-0">
                        <small class="text-muted">
                            @if($summary['income'] > 0)
                                You spent <strong>{{ number_format($expensePercentage, 1) }}%</strong> of your income
                                @if($summary['net'] > 0)
                                    , saving <strong>{{ \App\Helpers\CurrencyHelper::format($summary['net'], $currencyCode) }}</strong>
                                @else
                                    , with a deficit of <strong>{{ \App\Helpers\CurrencyHelper::format(abs($summary['net']), $currencyCode) }}</strong>
                                @endif
                            @else
                                No income recorded this month
                            @endif
                        </small>
                    </p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Monthly Income vs Expense Trend</h4>
            </div>
            <div class="card-body">
                <div id="income-expense-chart" style="height: 350px;"></div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Expense Types Breakdown</h4>
            </div>
            <div class="card-body">
                <div id="expense-types-chart" style="height: 300px;"></div>
            </div>
        </div>
    </div>
    <div class="col-xl-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Income Sources Breakdown</h4>
            </div>
            <div class="card-body">
                <div id="income-sources-chart" style="height: 300px;"></div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-4">Quick Actions</h4>
                <div class="row">
                    <div class="col-md-3">
                        <a href="{{ route('transactions.create') }}" class="btn btn-primary w-100 mb-3">
                            <i data-feather="plus" class="align-middle me-1"></i> New Transaction
                        </a>
                    </div>
                    @php
                        $accountContext = app(\App\Services\AccountContext::class);
                        $accountId = $accountContext->id();
                        $user = auth()->user();
                    @endphp
                    @if($accountId && $user->hasPermissionInAccount($accountId, 'wallets.create'))
                    <div class="col-md-3">
                        <a href="{{ route('wallets.create') }}" class="btn btn-success w-100 mb-3">
                            <i data-feather="trending-up" class="align-middle me-1"></i> Add Income Source
                        </a>
                    </div>
                    @endif
                    @if($accountId && $user->hasPermissionInAccount($accountId, 'categories.create'))
                    <div class="col-md-3">
                        <a href="{{ route('categories.create') }}" class="btn btn-info w-100 mb-3">
                            <i data-feather="tag" class="align-middle me-1"></i> Add Expense Type
                        </a>
                    </div>
                    @endif
                    <div class="col-md-3">
                        <a href="{{ route('reports.index') }}" class="btn btn-warning w-100 mb-3">
                            <i data-feather="bar-chart-2" class="align-middle me-1"></i> View Reports
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    @php
        $hasMonthlyData = isset($monthlyData) && !empty($monthlyData);
        $hasTopCategories = !empty($topCategories) && count($topCategories) > 0;
        $hasWalletBalances = !empty($walletBalances) && count($walletBalances) > 0;
    @endphp

    // Income vs Expense Donut Chart
    @if($summary['income'] > 0 || $summary['expense'] > 0)
    var donutChartOptions = {
        series: [{{ $summary['expense'] }}, {{ max(0, $summary['income'] - $summary['expense']) }}],
        chart: {
            type: 'donut',
            height: 350,
            toolbar: { show: false }
        },
        labels: ['Expense', 'Remaining'],
        colors: ['#dc3545', '#28a745'],
        plotOptions: {
            pie: {
                donut: {
                    size: '70%',
                    labels: {
                        show: true,
                        total: {
                            show: true,
                            label: 'Total Income',
                            fontSize: '16px',
                            fontWeight: 600,
                            color: '#495057',
                            formatter: function() {
                                return '{{ number_format($summary['income'], 2) }} {{ $currencyCode }}';
                            }
                        }
                    }
                }
            }
        },
        dataLabels: {
            enabled: true,
            formatter: function(val, opts) {
                return opts.w.globals.series[opts.seriesIndex].toFixed(2) + ' {{ $currencyCode }}';
            },
            style: {
                fontSize: '12px',
                fontWeight: 600
            }
        },
        legend: {
            position: 'bottom',
            horizontalAlign: 'center'
        },
        tooltip: {
            y: {
                formatter: function(val) {
                    return val.toFixed(2) + ' {{ $currencyCode }}';
                }
            }
        }
    };
    var donutChart = new ApexCharts(document.querySelector("#income-expense-donut-chart"), donutChartOptions);
    donutChart.render();
    @else
    document.getElementById('income-expense-donut-chart').innerHTML = '<p class="text-muted text-center mt-5">No financial data available this month</p>';
    @endif

    // Income vs Expense Line Chart
    @if($hasMonthlyData)
    var lineChartOptions = {
        series: [{
            name: 'Income',
            data: [{{ implode(',', array_column($monthlyData, 'income')) }}]
        }, {
            name: 'Expense',
            data: [{{ implode(',', array_column($monthlyData, 'expense')) }}]
        }],
        chart: {
            height: 350,
            type: 'line',
            toolbar: { show: false }
        },
        colors: ['#28a745', '#dc3545'],
        dataLabels: { enabled: false },
        stroke: { curve: 'smooth', width: 3 },
        xaxis: {
            categories: [@foreach($monthlyData as $data)'{{ $data['month'] }}',@endforeach]
        },
        yaxis: {
            labels: {
                formatter: function(val) {
                    return val.toFixed(2) + ' {{ $currencyCode }}';
                }
            }
        },
        legend: { position: 'top' }
    };
    var lineChart = new ApexCharts(document.querySelector("#income-expense-chart"), lineChartOptions);
    lineChart.render();
    @else
    document.getElementById('income-expense-chart').innerHTML = '<p class="text-muted text-center mt-5">No data available for chart</p>';
    @endif

    // Expense Types Pie Chart
    @if($hasTopCategories)
    var pieChartOptions = {
        series: [{{ implode(',', array_column($topCategories, 'total')) }}],
        chart: { type: 'pie', height: 300 },
        labels: [@foreach($topCategories as $cat)'{{ $cat['category'] }}',@endforeach],
        colors: ['#f06292', '#4fc3f7', '#ffa726', '#66bb6a', '#ef5350', '#ab47bc'],
        legend: { position: 'bottom' }
    };
    var pieChart = new ApexCharts(document.querySelector("#expense-types-chart"), pieChartOptions);
    pieChart.render();
    @else
    document.getElementById('expense-types-chart').innerHTML = '<p class="text-muted text-center mt-5">No expense data available</p>';
    @endif

    // Income Sources Bar Chart
    @if($hasWalletBalances)
    var barChartOptions = {
        series: [{
            name: 'Balance',
            data: [{{ implode(',', array_column($walletBalances, 'balance')) }}]
        }],
        chart: { type: 'bar', height: 300, toolbar: { show: false } },
        colors: ['#28a745'],
        xaxis: {
            categories: [@foreach($walletBalances as $wallet)'{{ $wallet['wallet'] }}',@endforeach]
        },
        yaxis: {
            labels: {
                formatter: function(val) {
                    return val.toFixed(2) + ' {{ $currencyCode }}';
                }
            }
        },
        plotOptions: { bar: { horizontal: false, columnWidth: '55%' } }
    };
    var barChart = new ApexCharts(document.querySelector("#income-sources-chart"), barChartOptions);
    barChart.render();
    @else
    document.getElementById('income-sources-chart').innerHTML = '<p class="text-muted text-center mt-5">No income source data available</p>';
    @endif
});
</script>
@endpush
@endsection
