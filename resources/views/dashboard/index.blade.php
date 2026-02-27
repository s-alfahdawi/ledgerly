@extends('layouts.app')

@section('title', 'Dashboard')

@php
    $currencyCode = $__currencyCode;
    $account = $__account;
@endphp

@section('content')
<div x-data="dashboardWidgets()" x-init="init()">

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Dashboard</h4>
            <div class="page-title-right">
                <div class="dropdown d-inline-block">
                    <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                        <i data-feather="sliders" class="icon-xs me-1"></i> Customize
                    </button>
                    <div class="dropdown-menu dropdown-menu-end p-3" style="min-width: 260px;">
                        <h6 class="dropdown-header px-0">Show / Hide Widgets</h6>
                        <template x-for="widget in widgetList" :key="widget.key">
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" :id="'toggle-' + widget.key" :checked="widgets[widget.key]" @change="toggle(widget.key)">
                                <label class="form-check-label" :for="'toggle-' + widget.key" x-text="widget.label"></label>
                            </div>
                        </template>
                        <div class="dropdown-divider"></div>
                        <button class="btn btn-sm btn-outline-primary w-100" @click="resetAll()">Show All</button>
                    </div>
                </div>
            </div>
        </div>
        <p class="text-muted mb-4">Overview of your financial activity. Track your income, expenses, and balances at a glance.</p>
    </div>
</div>

{{-- All-Time Summary Cards --}}
<div x-show="widgets.alltime" x-transition>
<h6 class="text-muted text-uppercase fw-semibold mb-3"><i data-feather="globe" class="icon-xs me-1"></i> All-Time Summary</h6>
<div class="row">
    <div class="col-xl-4 col-md-6">
        <div class="card border-start border-success border-3">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 me-3">
                        <div class="avatar-sm">
                            <span class="avatar-title bg-success-subtle text-success rounded-circle font-size-18">
                                <i data-feather="trending-up"></i>
                            </span>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <span class="text-muted text-uppercase font-size-12 fw-semibold">Total Income</span>
                        <h4 class="mb-0 text-success">{{ \App\Helpers\CurrencyHelper::format($allTimeSummary['income'], $currencyCode) }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-md-6">
        <div class="card border-start border-danger border-3">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 me-3">
                        <div class="avatar-sm">
                            <span class="avatar-title bg-danger-subtle text-danger rounded-circle font-size-18">
                                <i data-feather="trending-down"></i>
                            </span>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <span class="text-muted text-uppercase font-size-12 fw-semibold">Total Expense</span>
                        <h4 class="mb-0 text-danger">{{ \App\Helpers\CurrencyHelper::format($allTimeSummary['expense'], $currencyCode) }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-md-6">
        <div class="card border-start border-primary border-3">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 me-3">
                        <div class="avatar-sm">
                            <span class="avatar-title bg-primary-subtle text-primary rounded-circle font-size-18">
                                <i data-feather="briefcase"></i>
                            </span>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <span class="text-muted text-uppercase font-size-12 fw-semibold">Current Cash</span>
                        <h4 class="mb-0 {{ $allTimeSummary['current_cash'] >= 0 ? 'text-primary' : 'text-danger' }}">
                            {{ \App\Helpers\CurrencyHelper::format($allTimeSummary['current_cash'], $currencyCode) }}
                        </h4>
                        <small class="text-muted">Income - Expense</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</div>

{{-- Current Month Summary Cards --}}
<div x-show="widgets.monthly" x-transition>
<h6 class="text-muted text-uppercase fw-semibold mb-3"><i data-feather="calendar" class="icon-xs me-1"></i> This Month ({{ now($account->timezone ?? 'UTC')->format('F Y') }})</h6>
<div class="row">
    <div class="col-xl-4 col-md-6">
        <div class="card border-start border-success border-3">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 me-3">
                        <div class="avatar-sm">
                            <span class="avatar-title bg-success-subtle text-success rounded-circle font-size-18">
                                <i data-feather="arrow-up-circle"></i>
                            </span>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <span class="text-muted text-uppercase font-size-12 fw-semibold">Monthly Income</span>
                        <h4 class="mb-0 text-success">{{ \App\Helpers\CurrencyHelper::format($summary['income'], $currencyCode) }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-md-6">
        <div class="card border-start border-danger border-3">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 me-3">
                        <div class="avatar-sm">
                            <span class="avatar-title bg-danger-subtle text-danger rounded-circle font-size-18">
                                <i data-feather="arrow-down-circle"></i>
                            </span>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <span class="text-muted text-uppercase font-size-12 fw-semibold">Monthly Expense</span>
                        <h4 class="mb-0 text-danger">{{ \App\Helpers\CurrencyHelper::format($summary['expense'], $currencyCode) }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-md-6">
        <div class="card border-start border-info border-3">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 me-3">
                        <div class="avatar-sm">
                            <span class="avatar-title bg-info-subtle text-info rounded-circle font-size-18">
                                <i data-feather="activity"></i>
                            </span>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <span class="text-muted text-uppercase font-size-12 fw-semibold">Monthly Net</span>
                        <h4 class="mb-0 {{ $summary['net'] >= 0 ? 'text-info' : 'text-danger' }}">
                            {{ \App\Helpers\CurrencyHelper::format($summary['net'], $currencyCode) }}
                        </h4>
                        <small class="text-muted">Income - Expense</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</div>

{{-- Income & Expense Category Totals Tables --}}
<div class="row" x-show="widgets.tables" x-transition>
    <div class="col-xl-6">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-4">
                    <i data-feather="arrow-up-circle" class="icon-xs text-success me-1"></i>
                    Income by Source
                </h4>
                @if(!empty($incomeWalletTotals) && count($incomeWalletTotals) > 0)
                    <div class="table-responsive">
                        <table class="table table-hover table-nowrap align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Source</th>
                                    <th class="text-center">Transactions</th>
                                    <th class="text-end">Total Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($incomeWalletTotals as $index => $wallet)
                                    <tr>
                                        <td class="text-muted">{{ $index + 1 }}</td>
                                        <td>
                                            <span class="fw-medium">{{ $wallet['wallet'] }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-success-subtle text-success">{{ $wallet['transaction_count'] }}</span>
                                        </td>
                                        <td class="text-end">
                                            <strong class="text-success">{{ \App\Helpers\CurrencyHelper::format($wallet['total'], $currencyCode) }}</strong>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="2"><strong>Total</strong></td>
                                    <td class="text-center"><strong>{{ array_sum(array_column($incomeWalletTotals, 'transaction_count')) }}</strong></td>
                                    <td class="text-end"><strong class="text-success">{{ \App\Helpers\CurrencyHelper::format(array_sum(array_column($incomeWalletTotals, 'total')), $currencyCode) }}</strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @else
                    <p class="text-muted mb-0">No income transactions recorded yet.</p>
                @endif
            </div>
        </div>
    </div>
    <div class="col-xl-6">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-4">
                    <i data-feather="arrow-down-circle" class="icon-xs text-danger me-1"></i>
                    Expense by Category
                </h4>
                @if(!empty($expenseCategoryTotals) && count($expenseCategoryTotals) > 0)
                    <div class="table-responsive">
                        <table class="table table-hover table-nowrap align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Category</th>
                                    <th class="text-center">Transactions</th>
                                    <th class="text-end">Total Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($expenseCategoryTotals as $index => $cat)
                                    <tr>
                                        <td class="text-muted">{{ $index + 1 }}</td>
                                        <td>
                                            <span class="fw-medium">{{ $cat['category'] }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-danger-subtle text-danger">{{ $cat['transaction_count'] }}</span>
                                        </td>
                                        <td class="text-end">
                                            <strong class="text-danger">{{ \App\Helpers\CurrencyHelper::format($cat['total'], $currencyCode) }}</strong>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="2"><strong>Total</strong></td>
                                    <td class="text-center"><strong>{{ array_sum(array_column($expenseCategoryTotals, 'transaction_count')) }}</strong></td>
                                    <td class="text-end"><strong class="text-danger">{{ \App\Helpers\CurrencyHelper::format(array_sum(array_column($expenseCategoryTotals, 'total')), $currencyCode) }}</strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @else
                    <p class="text-muted mb-0">No expense transactions recorded yet.</p>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row" x-show="widgets.ratio_chart" x-transition>
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
                <div id="income-expense-chart" style="height: 350px;">
                    <div class="skeleton skeleton-chart" style="height: 350px;"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row" x-show="widgets.breakdown_charts" x-transition>
    <div class="col-xl-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Expense Types Breakdown</h4>
            </div>
            <div class="card-body">
                <div id="expense-types-chart" style="height: 300px;">
                    <div class="skeleton skeleton-chart" style="height: 300px;"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Income Sources Breakdown</h4>
            </div>
            <div class="card-body">
                <div id="income-sources-chart" style="height: 300px;">
                    <div class="skeleton skeleton-chart" style="height: 300px;"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row" x-show="widgets.quick_actions" x-transition>
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
                        $user = auth()->user();
                    @endphp
                    @if($__accountId && $user->hasPermissionInAccount($__accountId, 'wallets.create'))
                    <div class="col-md-3">
                        <a href="{{ route('wallets.create') }}" class="btn btn-success w-100 mb-3">
                            <i data-feather="trending-up" class="align-middle me-1"></i> Add Income Source
                        </a>
                    </div>
                    @endif
                    @if($__accountId && $user->hasPermissionInAccount($__accountId, 'categories.create'))
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

{{-- Close the x-data wrapper --}}
</div>

@push('scripts')
<script>
function dashboardWidgets() {
    var storageKey = 'ledgerly_dashboard_widgets';
    var defaults = {
        alltime: true,
        monthly: true,
        tables: true,
        ratio_chart: true,
        breakdown_charts: true,
        quick_actions: true
    };
    return {
        widgets: Object.assign({}, defaults),
        widgetList: [
            { key: 'alltime', label: 'All-Time Summary' },
            { key: 'monthly', label: 'This Month Summary' },
            { key: 'tables', label: 'Income & Expense Tables' },
            { key: 'ratio_chart', label: 'Ratio & Trend Charts' },
            { key: 'breakdown_charts', label: 'Breakdown Charts' },
            { key: 'quick_actions', label: 'Quick Actions' }
        ],
        init: function() {
            try {
                var saved = localStorage.getItem(storageKey);
                if (saved) {
                    var parsed = JSON.parse(saved);
                    this.widgets = Object.assign({}, defaults, parsed);
                }
            } catch (e) { /* ignore parse errors */ }
        },
        toggle: function(key) {
            this.widgets[key] = !this.widgets[key];
            this.save();
        },
        save: function() {
            try {
                localStorage.setItem(storageKey, JSON.stringify(this.widgets));
            } catch (e) { /* ignore storage errors */ }
        },
        resetAll: function() {
            this.widgets = Object.assign({}, defaults);
            this.save();
        }
    };
}

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
