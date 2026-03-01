@extends('layouts.app')

@section('title', 'Dashboard')

@php
    $currencyCode = $__currencyCode;
    $account = $__account;

    // Trend helpers
    $incomeDiff = $summary['income'] - ($lastMonthSummary['income'] ?? 0);
    $expenseDiff = $summary['expense'] - ($lastMonthSummary['expense'] ?? 0);

    $incomePct = ($lastMonthSummary['income'] ?? 0) > 0 ? round(($incomeDiff / $lastMonthSummary['income']) * 100, 1) : ($summary['income'] > 0 ? 100 : 0);
    $expensePct = ($lastMonthSummary['expense'] ?? 0) > 0 ? round(($expenseDiff / $lastMonthSummary['expense']) * 100, 1) : ($summary['expense'] > 0 ? 100 : 0);
    $savingsRate = $summary['income'] > 0 ? round(($summary['net'] / $summary['income']) * 100, 1) : 0;
@endphp

@section('content')
<div x-data="dashboardWidgets()" x-init="init()">

{{-- Page Header --}}
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <div>
                <h4 class="mb-1">Dashboard</h4>
                <p class="text-muted mb-0">{{ now($account->timezone ?? 'UTC')->format('l, F j, Y') }}</p>
            </div>
            <div class="page-title-right d-flex gap-2">
                <a href="{{ route('transactions.create') }}" class="btn btn-primary btn-sm">
                    <i data-feather="plus" class="icon-xs me-1"></i> New Transaction
                </a>
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
    </div>
</div>

{{-- ============================================================== --}}
{{-- KEY METRICS ROW --}}
{{-- ============================================================== --}}
<div x-show="widgets.monthly" x-transition>
<div class="row">
    {{-- Monthly Income --}}
    <div class="col-xl-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-start justify-content-between">
                    <div>
                        <p class="text-muted fw-semibold font-size-13 mb-1">Monthly Income</p>
                        <h4 class="mb-1 text-success">{{ \App\Helpers\CurrencyHelper::format($summary['income'], $currencyCode) }}</h4>
                        @if($lastMonthSummary['income'] > 0 || $summary['income'] > 0)
                        <p class="mb-0 font-size-12">
                            <span class="text-{{ $incomeDiff >= 0 ? 'success' : 'danger' }}">
                                <i class="mdi mdi-arrow-{{ $incomeDiff >= 0 ? 'up' : 'down' }}-bold"></i>
                                {{ abs($incomePct) }}%
                            </span>
                            <span class="text-muted ms-1">vs last month</span>
                        </p>
                        @endif
                    </div>
                    <div class="avatar-sm">
                        <span class="avatar-title bg-success-subtle text-success rounded">
                            <i data-feather="trending-up" class="font-size-20"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Monthly Expense --}}
    <div class="col-xl-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-start justify-content-between">
                    <div>
                        <p class="text-muted fw-semibold font-size-13 mb-1">Monthly Expense</p>
                        <h4 class="mb-1 text-danger">{{ \App\Helpers\CurrencyHelper::format($summary['expense'], $currencyCode) }}</h4>
                        @if($lastMonthSummary['expense'] > 0 || $summary['expense'] > 0)
                        <p class="mb-0 font-size-12">
                            <span class="text-{{ $expenseDiff <= 0 ? 'success' : 'danger' }}">
                                <i class="mdi mdi-arrow-{{ $expenseDiff <= 0 ? 'down' : 'up' }}-bold"></i>
                                {{ abs($expensePct) }}%
                            </span>
                            <span class="text-muted ms-1">vs last month</span>
                        </p>
                        @endif
                    </div>
                    <div class="avatar-sm">
                        <span class="avatar-title bg-danger-subtle text-danger rounded">
                            <i data-feather="trending-down" class="font-size-20"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Net Savings --}}
    <div class="col-xl-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-start justify-content-between">
                    <div>
                        <p class="text-muted fw-semibold font-size-13 mb-1">Net Savings</p>
                        <h4 class="mb-1 {{ $summary['net'] >= 0 ? 'text-primary' : 'text-danger' }}">{{ \App\Helpers\CurrencyHelper::format($summary['net'], $currencyCode) }}</h4>
                        @if($summary['income'] > 0)
                        <p class="mb-0 font-size-12">
                            <span class="text-{{ $savingsRate >= 0 ? 'success' : 'danger' }}">{{ $savingsRate }}% savings rate</span>
                        </p>
                        @endif
                    </div>
                    <div class="avatar-sm">
                        <span class="avatar-title bg-primary-subtle text-primary rounded">
                            <i data-feather="pocket" class="font-size-20"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Total Balance --}}
    <div class="col-xl-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-start justify-content-between">
                    <div>
                        <p class="text-muted fw-semibold font-size-13 mb-1">Total Balance</p>
                        <h4 class="mb-1 {{ $totalBalance >= 0 ? 'text-info' : 'text-danger' }}">{{ \App\Helpers\CurrencyHelper::format($totalBalance, $currencyCode) }}</h4>
                        <p class="mb-0 font-size-12">
                            <span class="text-muted">{{ $transactionCount ?? 0 }} txns this month</span>
                        </p>
                    </div>
                    <div class="avatar-sm">
                        <span class="avatar-title bg-info-subtle text-info rounded">
                            <i data-feather="briefcase" class="font-size-20"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

{{-- ============================================================== --}}
{{-- CHARTS ROW - Income/Expense Trend + Donut --}}
{{-- ============================================================== --}}
<div class="row" x-show="widgets.ratio_chart" x-transition>
    <div class="col-xl-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">Income vs Expense Trend</h4>
                <span class="badge bg-light text-muted">Last 12 months</span>
            </div>
            <div class="card-body">
                <div id="income-expense-chart" style="height: 370px;">
                    <div class="skeleton skeleton-chart" style="height: 370px;"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">This Month Ratio</h4>
            </div>
            <div class="card-body">
                <div id="income-expense-donut-chart" style="height: 250px;"></div>
                <div class="mt-3">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="font-size-13"><span class="d-inline-block rounded-circle me-1" style="width:10px;height:10px;background:#0ab39c"></span> Income</span>
                        <strong>{{ \App\Helpers\CurrencyHelper::format($summary['income'], $currencyCode) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="font-size-13"><span class="d-inline-block rounded-circle me-1" style="width:10px;height:10px;background:#f06548"></span> Expense</span>
                        <strong>{{ \App\Helpers\CurrencyHelper::format($summary['expense'], $currencyCode) }}</strong>
                    </div>
                    <hr class="my-2">
                    <div class="d-flex justify-content-between">
                        <span class="font-size-13 fw-semibold">Net</span>
                        <strong class="{{ $summary['net'] >= 0 ? 'text-success' : 'text-danger' }}">{{ \App\Helpers\CurrencyHelper::format($summary['net'], $currencyCode) }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ============================================================== --}}
{{-- WALLET BALANCES + TOP EXPENSE CATEGORIES --}}
{{-- ============================================================== --}}
<div class="row" x-show="widgets.breakdown_charts" x-transition>
    <div class="col-xl-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0"><i data-feather="credit-card" class="icon-xs me-1"></i> Wallet Balances</h4>
                <a href="{{ route('wallets.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body">
                @if(!empty($walletBalances))
                    @php $maxBalance = max(1, max(array_map('abs', array_column($walletBalances, 'balance')))); @endphp
                    @foreach($walletBalances as $wb)
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="fw-medium">{{ $wb['wallet'] }}</span>
                                <span class="fw-semibold {{ $wb['balance'] >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ \App\Helpers\CurrencyHelper::format($wb['balance'], $currencyCode) }}
                                </span>
                            </div>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar {{ $wb['balance'] >= 0 ? 'bg-success' : 'bg-danger' }}"
                                     style="width: {{ $maxBalance > 0 ? min(abs($wb['balance']) / $maxBalance * 100, 100) : 0 }}%"></div>
                            </div>
                        </div>
                    @endforeach
                    <hr>
                    <div class="d-flex justify-content-between">
                        <span class="fw-semibold">Total</span>
                        <span class="fw-bold {{ $totalBalance >= 0 ? 'text-success' : 'text-danger' }}">
                            {{ \App\Helpers\CurrencyHelper::format($totalBalance, $currencyCode) }}
                        </span>
                    </div>
                @else
                    <p class="text-muted mb-0">No wallets configured. <a href="{{ route('wallets.create') }}">Add one</a></p>
                @endif
            </div>
        </div>
    </div>

    <div class="col-xl-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0"><i data-feather="pie-chart" class="icon-xs me-1"></i> Top Expenses This Month</h4>
                <a href="{{ route('reports.index') }}" class="btn btn-sm btn-outline-primary">Full Report</a>
            </div>
            <div class="card-body">
                @if(!empty($topCategories))
                    @foreach($topCategories as $cat)
                        @php $pct = $summary['expense'] > 0 ? round(($cat['total'] / $summary['expense']) * 100, 1) : 0; @endphp
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="fw-medium">{{ $cat['category'] }}</span>
                                <span>
                                    <span class="text-danger fw-semibold">{{ \App\Helpers\CurrencyHelper::format($cat['total'], $currencyCode) }}</span>
                                    <span class="text-muted font-size-12 ms-1">({{ $pct }}%)</span>
                                </span>
                            </div>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar bg-danger" style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <p class="text-muted mb-0">No expenses recorded this month.</p>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- ============================================================== --}}
{{-- BUDGET PROGRESS + RECENT TRANSACTIONS --}}
{{-- ============================================================== --}}
<div class="row" x-show="widgets.tables" x-transition>
    <div class="col-xl-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0"><i data-feather="target" class="icon-xs me-1"></i> Budget Progress</h4>
                <a href="{{ route('budgets.index') }}" class="btn btn-sm btn-outline-primary">Manage</a>
            </div>
            <div class="card-body">
                @if(!empty($budgetProgress))
                    @foreach($budgetProgress as $budget)
                        @php $barColor = $budget['percentage'] >= 90 ? 'danger' : ($budget['percentage'] >= 70 ? 'warning' : 'success'); @endphp
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="fw-medium">{{ $budget['category'] }}</span>
                                <span class="font-size-12">
                                    <span class="{{ $budget['over_budget'] ? 'text-danger fw-bold' : 'text-muted' }}">
                                        {{ \App\Helpers\CurrencyHelper::format($budget['spent'], $currencyCode) }}
                                    </span>
                                    / {{ \App\Helpers\CurrencyHelper::format($budget['limit'], $currencyCode) }}
                                </span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-{{ $barColor }}" style="width: {{ min($budget['percentage'], 100) }}%"></div>
                            </div>
                            @if($budget['over_budget'])
                                <small class="text-danger fw-semibold">Over budget!</small>
                            @else
                                <small class="text-muted">{{ \App\Helpers\CurrencyHelper::format($budget['remaining'], $currencyCode) }} remaining</small>
                            @endif
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-3">
                        <p class="text-muted mb-2">No budgets set up yet.</p>
                        <a href="{{ route('budgets.create') }}" class="btn btn-sm btn-outline-primary">Create Budget</a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-xl-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0"><i data-feather="clock" class="icon-xs me-1"></i> Recent Transactions</h4>
                <a href="{{ route('transactions.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0">
                @if(isset($recentTransactions) && $recentTransactions->isNotEmpty())
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <tbody>
                                @foreach($recentTransactions as $txn)
                                    <tr>
                                        <td style="width: 40px;" class="ps-3">
                                            <div class="avatar-xs">
                                                <span class="avatar-title bg-{{ $txn->type === 'income' ? 'success' : ($txn->type === 'expense' ? 'danger' : 'info') }}-subtle text-{{ $txn->type === 'income' ? 'success' : ($txn->type === 'expense' ? 'danger' : 'info') }} rounded-circle">
                                                    <i data-feather="{{ $txn->type === 'income' ? 'arrow-up' : ($txn->type === 'expense' ? 'arrow-down' : 'repeat') }}" class="font-size-14"></i>
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            <h6 class="mb-0 font-size-13">{{ $txn->category?->name ?? $txn->wallet?->name ?? ucfirst($txn->type) }}</h6>
                                            <small class="text-muted">{{ $txn->occurred_at->format('M d') }}@if($txn->note) &middot; {{ Str::limit($txn->note, 25) }}@endif</small>
                                        </td>
                                        <td class="text-end pe-3">
                                            <span class="fw-semibold {{ $txn->type === 'income' ? 'text-success' : 'text-danger' }}">
                                                {{ $txn->type === 'income' ? '+' : '-' }}{{ \App\Helpers\CurrencyHelper::format($txn->amount, $currencyCode) }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <p class="text-muted mb-2">No transactions yet.</p>
                        <a href="{{ route('transactions.create') }}" class="btn btn-sm btn-primary">Add Your First</a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- ============================================================== --}}
{{-- QUICK ACTIONS --}}
{{-- ============================================================== --}}
<div class="row" x-show="widgets.quick_actions" x-transition>
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-3">Quick Actions</h4>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('transactions.create') }}" class="btn btn-primary">
                        <i data-feather="plus" class="icon-xs me-1"></i> New Transaction
                    </a>
                    @php $user = auth()->user(); @endphp
                    @if($__accountId && $user->hasPermissionInAccount($__accountId, 'wallets.create'))
                    <a href="{{ route('wallets.create') }}" class="btn btn-success">
                        <i data-feather="trending-up" class="icon-xs me-1"></i> Add Income Source
                    </a>
                    @endif
                    @if($__accountId && $user->hasPermissionInAccount($__accountId, 'categories.create'))
                    <a href="{{ route('categories.create') }}" class="btn btn-info">
                        <i data-feather="tag" class="icon-xs me-1"></i> Add Expense Type
                    </a>
                    @endif
                    <a href="{{ route('reports.index') }}" class="btn btn-warning">
                        <i data-feather="bar-chart-2" class="icon-xs me-1"></i> View Reports
                    </a>
                    <a href="{{ route('budgets.index') }}" class="btn btn-outline-secondary">
                        <i data-feather="target" class="icon-xs me-1"></i> Budgets
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ============================================================== --}}
{{-- ALL-TIME SUMMARY --}}
{{-- ============================================================== --}}
<div x-show="widgets.alltime" x-transition>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0"><i data-feather="globe" class="icon-xs me-1"></i> All-Time Summary</h4>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-4">
                        <h5 class="text-success mb-1">{{ \App\Helpers\CurrencyHelper::format($allTimeSummary['income'], $currencyCode) }}</h5>
                        <p class="text-muted mb-0">Total Income</p>
                    </div>
                    <div class="col-md-4">
                        <h5 class="text-danger mb-1">{{ \App\Helpers\CurrencyHelper::format($allTimeSummary['expense'], $currencyCode) }}</h5>
                        <p class="text-muted mb-0">Total Expense</p>
                    </div>
                    <div class="col-md-4">
                        <h5 class="{{ $allTimeSummary['current_cash'] >= 0 ? 'text-primary' : 'text-danger' }} mb-1">{{ \App\Helpers\CurrencyHelper::format($allTimeSummary['current_cash'], $currencyCode) }}</h5>
                        <p class="text-muted mb-0">Net Cash</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

{{-- ============================================================== --}}
{{-- BREAKDOWN CHARTS (Donut + Bar) --}}
{{-- ============================================================== --}}
<div class="row" x-show="widgets.breakdown_pie" x-transition>
    <div class="col-xl-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Expense Category Breakdown</h4>
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
                <h4 class="card-title mb-0">Income Source Balances</h4>
            </div>
            <div class="card-body">
                <div id="income-sources-chart" style="height: 300px;">
                    <div class="skeleton skeleton-chart" style="height: 300px;"></div>
                </div>
            </div>
        </div>
    </div>
</div>

</div>{{-- /x-data --}}

@push('scripts')
<script>
function dashboardWidgets() {
    var storageKey = 'ledgerly_dashboard_widgets';
    var defaults = {
        monthly: true,
        ratio_chart: true,
        breakdown_charts: true,
        tables: true,
        alltime: false,
        breakdown_pie: true,
        quick_actions: true
    };
    return {
        widgets: Object.assign({}, defaults),
        widgetList: [
            { key: 'monthly', label: 'Key Metrics' },
            { key: 'ratio_chart', label: 'Trend Charts' },
            { key: 'breakdown_charts', label: 'Wallets & Top Expenses' },
            { key: 'tables', label: 'Budgets & Recent Transactions' },
            { key: 'quick_actions', label: 'Quick Actions' },
            { key: 'alltime', label: 'All-Time Summary' },
            { key: 'breakdown_pie', label: 'Category & Source Charts' }
        ],
        init: function() {
            try {
                var saved = localStorage.getItem(storageKey);
                if (saved) {
                    var parsed = JSON.parse(saved);
                    this.widgets = Object.assign({}, defaults, parsed);
                }
            } catch (e) {}
            // Initialize charts after Alpine has rendered the DOM
            this.$nextTick(function() {
                window._initDashboardCharts();
            });
        },
        toggle: function(key) {
            this.widgets[key] = !this.widgets[key];
            this.save();
            // Re-render charts when toggling visibility (ApexCharts needs resize)
            if (this.widgets[key]) {
                var self = this;
                this.$nextTick(function() {
                    window.dispatchEvent(new Event('resize'));
                });
            }
        },
        save: function() {
            try { localStorage.setItem(storageKey, JSON.stringify(this.widgets)); } catch (e) {}
        },
        resetAll: function() {
            this.widgets = Object.assign({}, defaults);
            this.save();
            this.$nextTick(function() {
                window.dispatchEvent(new Event('resize'));
            });
        }
    };
}

window._initDashboardCharts = function() {
    @php
        $hasMonthlyData = isset($monthlyData) && !empty($monthlyData);
        $hasTopCategories = !empty($topCategories) && count($topCategories) > 0;
        $hasWalletBalances = !empty($walletBalances) && count($walletBalances) > 0;
    @endphp

    var chartTheme = {
        income: '#0ab39c',
        expense: '#f06548',
        net: '#405189',
        info: '#299cdb',
        warning: '#f7b84b',
        grid: '#f1f1f1'
    };

    // ── Donut: Income vs Expense ──
    try {
    @if($summary['income'] > 0 || $summary['expense'] > 0)
    var donutEl = document.querySelector("#income-expense-donut-chart");
    if (donutEl) {
        donutEl.innerHTML = '';
        new ApexCharts(donutEl, {
            series: [{{ $summary['income'] }}, {{ $summary['expense'] }}],
            chart: { type: 'donut', height: 250 },
            labels: ['Income', 'Expense'],
            colors: [chartTheme.income, chartTheme.expense],
            plotOptions: {
                pie: {
                    donut: {
                        size: '72%',
                        labels: {
                            show: true,
                            total: {
                                show: true,
                                label: 'Net',
                                fontSize: '14px',
                                fontWeight: 600,
                                formatter: function() {
                                    return '{{ \App\Helpers\CurrencyHelper::format($summary["net"], $currencyCode) }}';
                                }
                            }
                        }
                    }
                }
            },
            dataLabels: { enabled: false },
            legend: { show: false },
            tooltip: {
                y: { formatter: function(val) { return val.toLocaleString() + ' {{ $currencyCode }}'; } }
            }
        }).render();
    }
    @else
    var donutEl = document.getElementById('income-expense-donut-chart');
    if (donutEl) donutEl.innerHTML = '<p class="text-muted text-center py-5">No data this month</p>';
    @endif
    } catch(e) { console.warn('Donut chart error:', e); }

    // ── Area Chart: 12-Month Trend ──
    try {
    @if($hasMonthlyData)
    var areaEl = document.querySelector("#income-expense-chart");
    if (areaEl) {
        areaEl.innerHTML = '';
        new ApexCharts(areaEl, {
            series: [{
                name: 'Income',
                data: [{{ implode(',', array_column($monthlyData, 'income')) }}]
            }, {
                name: 'Expense',
                data: [{{ implode(',', array_column($monthlyData, 'expense')) }}]
            }, {
                name: 'Net',
                data: [{{ implode(',', array_column($monthlyData, 'net')) }}]
            }],
            chart: {
                height: 370,
                type: 'area',
                toolbar: { show: true, tools: { download: true, selection: false, zoom: false, zoomin: false, zoomout: false, pan: false, reset: false } },
                fontFamily: 'inherit'
            },
            colors: [chartTheme.income, chartTheme.expense, chartTheme.net],
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth', width: [2, 2, 2] },
            fill: {
                type: 'gradient',
                gradient: { shadeIntensity: 1, opacityFrom: 0.25, opacityTo: 0.05 }
            },
            xaxis: {
                categories: [@foreach($monthlyData as $d)'{{ $d["month"] }}',@endforeach],
                labels: { style: { fontSize: '11px' } }
            },
            yaxis: {
                labels: {
                    formatter: function(val) { return val >= 1000000 ? (val/1000000).toFixed(1) + 'M' : val >= 1000 ? (val/1000).toFixed(1) + 'K' : val.toFixed(0); }
                }
            },
            legend: { position: 'top', horizontalAlign: 'right' },
            tooltip: {
                y: { formatter: function(val) { return val.toLocaleString() + ' {{ $currencyCode }}'; } }
            },
            grid: { borderColor: chartTheme.grid }
        }).render();
    }
    @else
    var areaEl = document.getElementById('income-expense-chart');
    if (areaEl) areaEl.innerHTML = '<p class="text-muted text-center py-5">No data available</p>';
    @endif
    } catch(e) { console.warn('Area chart error:', e); }

    // ── Donut: Expense Categories ──
    try {
    @if($hasTopCategories)
    var expenseEl = document.querySelector("#expense-types-chart");
    if (expenseEl) {
        expenseEl.innerHTML = '';
        new ApexCharts(expenseEl, {
            series: [{{ implode(',', array_column($topCategories, 'total')) }}],
            chart: { type: 'donut', height: 300 },
            labels: [@foreach($topCategories as $cat)'{{ addslashes($cat["category"]) }}',@endforeach],
            colors: ['#f06548', '#f7b84b', '#299cdb', '#0ab39c', '#405189', '#ab47bc'],
            plotOptions: {
                pie: {
                    donut: {
                        size: '65%',
                        labels: {
                            show: true,
                            total: {
                                show: true,
                                label: 'Total',
                                formatter: function(w) {
                                    return w.globals.seriesTotals.reduce(function(a, b) { return a + b; }, 0).toLocaleString() + ' {{ $currencyCode }}';
                                }
                            }
                        }
                    }
                }
            },
            dataLabels: { enabled: false },
            legend: { position: 'bottom' },
            tooltip: {
                y: { formatter: function(val) { return val.toLocaleString() + ' {{ $currencyCode }}'; } }
            }
        }).render();
    }
    @else
    var expenseEl = document.getElementById('expense-types-chart');
    if (expenseEl) expenseEl.innerHTML = '<p class="text-muted text-center py-5">No expense data</p>';
    @endif
    } catch(e) { console.warn('Expense chart error:', e); }

    // ── Bar Chart: Wallet/Income Source Balances ──
    try {
    @if($hasWalletBalances)
    var walletEl = document.querySelector("#income-sources-chart");
    if (walletEl) {
        walletEl.innerHTML = '';
        new ApexCharts(walletEl, {
            series: [{
                name: 'Balance',
                data: [{{ implode(',', array_column($walletBalances, 'balance')) }}]
            }],
            chart: { type: 'bar', height: 300, toolbar: { show: false } },
            colors: [chartTheme.income],
            xaxis: {
                categories: [@foreach($walletBalances as $w)'{{ addslashes($w["wallet"]) }}',@endforeach],
                labels: { style: { fontSize: '11px' } }
            },
            yaxis: {
                labels: {
                    formatter: function(val) { return val >= 1000000 ? (val/1000000).toFixed(1) + 'M' : val >= 1000 ? (val/1000).toFixed(1) + 'K' : val.toFixed(0); }
                }
            },
            plotOptions: { bar: { horizontal: false, columnWidth: '50%', borderRadius: 4 } },
            dataLabels: { enabled: false },
            tooltip: {
                y: { formatter: function(val) { return val.toLocaleString() + ' {{ $currencyCode }}'; } }
            },
            grid: { borderColor: chartTheme.grid }
        }).render();
    }
    @else
    var walletEl = document.getElementById('income-sources-chart');
    if (walletEl) walletEl.innerHTML = '<p class="text-muted text-center py-5">No wallet data</p>';
    @endif
    } catch(e) { console.warn('Wallet chart error:', e); }
};
</script>
@endpush
@endsection
