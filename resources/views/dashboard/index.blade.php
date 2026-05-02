@extends('layouts.app')

@section('title', 'Dashboard')

@php
    $currencyCode = $__currencyCode;
    $account = $__account;

    // Trend helpers — current month vs last month
    $incomeDiff  = $summary['income']  - ($lastMonthSummary['income']  ?? 0);
    $expenseDiff = $summary['expense'] - ($lastMonthSummary['expense'] ?? 0);

    $incomePct  = ($lastMonthSummary['income']  ?? 0) > 0 ? round(($incomeDiff  / $lastMonthSummary['income'])  * 100, 1) : ($summary['income']  > 0 ? 100 : 0);
    $expensePct = ($lastMonthSummary['expense'] ?? 0) > 0 ? round(($expenseDiff / $lastMonthSummary['expense']) * 100, 1) : ($summary['expense'] > 0 ? 100 : 0);
    $savingsRate = $summary['income'] > 0 ? round(($summary['net'] / $summary['income']) * 100, 1) : 0;

    // Prev month comparison helpers (for YTD stats section)
    $prevIncomeChange   = $summary['income']  - ($lastMonthSummary['income']  ?? 0);
    $prevExpenseChange  = $summary['expense'] - ($lastMonthSummary['expense'] ?? 0);
    $prevIncomePctRaw   = ($lastMonthSummary['income']  ?? 0) > 0 ? round(abs($prevIncomeChange  / $lastMonthSummary['income'])  * 100, 1) : ($summary['income']  > 0 ? 100 : 0);
    $prevExpensePctRaw  = ($lastMonthSummary['expense'] ?? 0) > 0 ? round(abs($prevExpenseChange / $lastMonthSummary['expense']) * 100, 1) : ($summary['expense'] > 0 ? 100 : 0);

    // Month-over-month comparison helpers
    $netDiff      = $summary['net'] - ($lastMonthSummary['net'] ?? 0);
    $txCountDiff  = ($transactionCount ?? 0) - ($lastMonthTxCount ?? 0);
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
                <div class="d-inline-block" style="position: relative; z-index: 1030;" x-data="{ open: false }">
                    <button class="btn btn-outline-secondary btn-sm" type="button" @click="open = !open">
                        <i data-feather="sliders" class="icon-xs me-1"></i> Customize
                    </button>
                    <div class="dropdown-menu dropdown-menu-end p-3" :class="{ show: open }" @click.outside="open = false" style="min-width: 260px;" x-show="open" x-transition>
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
{{-- ALL-TIME TOTALS (Income / Expense / Current Cash) --}}
{{-- ============================================================== --}}
<div x-show="widgets.alltime_totals" x-transition>
<div class="row">
    <div class="col-xl-4 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-start justify-content-between">
                    <div>
                        <p class="text-muted fw-semibold font-size-13 mb-1">All-Time Income</p>
                        <h4 class="mb-1 text-success">{{ \App\Helpers\CurrencyHelper::format($allTimeSummary['income'], $currencyCode) }}</h4>
                        <p class="mb-0 font-size-12 text-muted">{{ number_format($txCounts['income_count']) }} transactions</p>
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
    <div class="col-xl-4 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-start justify-content-between">
                    <div>
                        <p class="text-muted fw-semibold font-size-13 mb-1">All-Time Expense</p>
                        <h4 class="mb-1 text-danger">{{ \App\Helpers\CurrencyHelper::format($allTimeSummary['expense'], $currencyCode) }}</h4>
                        <p class="mb-0 font-size-12 text-muted">{{ number_format($txCounts['expense_count']) }} transactions</p>
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
    <div class="col-xl-4 col-md-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-start justify-content-between">
                    <div>
                        <p class="text-muted fw-semibold font-size-13 mb-1">Current Cash</p>
                        <h4 class="mb-1 {{ $allTimeSummary['current_cash'] >= 0 ? 'text-primary' : 'text-danger' }}">{{ \App\Helpers\CurrencyHelper::format($allTimeSummary['current_cash'], $currencyCode) }}</h4>
                        <p class="mb-0 font-size-12 text-muted">Income minus expense, all time</p>
                    </div>
                    <div class="avatar-sm">
                        <span class="avatar-title bg-primary-subtle text-primary rounded">
                            <i data-feather="dollar-sign" class="font-size-20"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

{{-- ============================================================== --}}
{{-- CASH MATCH (Reconcile actual cash on hand with system balance) --}}
{{-- ============================================================== --}}
<div x-show="widgets.cash_match" x-transition>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0"><i data-feather="check-circle" class="icon-xs me-1"></i> Match Cash on Hand</h4>
                <span class="badge bg-light text-muted">Creates an adjustment transaction</span>
            </div>
            <div class="card-body">
                @if(session('info'))
                    <div class="alert alert-info py-2 mb-3">{{ session('info') }}</div>
                @endif
                @if(empty($walletStats))
                    <p class="text-muted mb-0">Add at least one wallet/account to use cash matching. <a href="{{ route('wallets.create') }}">Add wallet</a></p>
                @else
                <form method="POST" action="{{ route('dashboard.cash-match') }}"
                      x-data="{
                          walletId: '{{ $walletStats[0]['id'] }}',
                          actual: '',
                          wallets: @js(array_map(fn($w) => ['id' => $w['id'], 'name' => $w['name'], 'balance' => $w['balance']], $walletStats)),
                          get current() {
                              var w = this.wallets.find(x => String(x.id) === String(this.walletId));
                              return w ? w.balance : 0;
                          },
                          get diff() {
                              var actual = parseFloat(this.actual);
                              if (isNaN(actual)) return null;
                              return Math.round((actual - this.current) * 100) / 100;
                          }
                      }">
                    @csrf
                    <div class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label">Wallet / Account</label>
                            <select name="wallet_id" class="form-select" x-model="walletId" required>
                                @foreach($walletStats as $w)
                                    <option value="{{ $w['id'] }}">{{ $w['name'] }}</option>
                                @endforeach
                            </select>
                            <small class="text-muted">
                                System balance: <strong x-text="current.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})"></strong> {{ $currencyCode }}
                            </small>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Actual Cash on Hand</label>
                            <input type="number" name="actual_cash" class="form-control" step="0.01" min="0" x-model="actual" placeholder="0.00" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Note (optional)</label>
                            <input type="text" name="note" class="form-control" maxlength="500" placeholder="Reason for adjustment">
                        </div>
                        <div class="col-md-2 d-grid">
                            <button type="submit" class="btn btn-primary" :disabled="diff === null || Math.abs(diff) < 0.01">
                                <i data-feather="check" class="icon-xs me-1"></i> Settle
                            </button>
                        </div>
                    </div>
                    <div class="mt-3 font-size-13" x-show="diff !== null" x-cloak>
                        <template x-if="Math.abs(diff) < 0.01">
                            <span class="text-success"><i class="mdi mdi-check-circle"></i> Already balanced — no transaction will be created.</span>
                        </template>
                        <template x-if="diff > 0.005">
                            <span>
                                Will create an
                                <span class="badge bg-success-subtle text-success">income</span>
                                adjustment of
                                <strong x-text="diff.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})"></strong>
                                {{ $currencyCode }} (you have more cash than the system shows).
                            </span>
                        </template>
                        <template x-if="diff < -0.005">
                            <span>
                                Will create an
                                <span class="badge bg-danger-subtle text-danger">expense</span>
                                adjustment of
                                <strong x-text="Math.abs(diff).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})"></strong>
                                {{ $currencyCode }} (you have less cash than the system shows).
                            </span>
                        </template>
                    </div>
                </form>
                @endif
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
{{-- YTD & AVERAGES ROW --}}
{{-- ============================================================== --}}
<div x-show="widgets.ytd_stats" x-transition>
<div class="row">
    {{-- YTD Income --}}
    <div class="col-xl-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-start justify-content-between">
                    <div>
                        <p class="text-muted fw-semibold font-size-13 mb-1">YTD Income</p>
                        <h4 class="mb-1 text-success">{{ \App\Helpers\CurrencyHelper::format($ytdSummary['income'], $currencyCode) }}</h4>
                        <p class="mb-0 font-size-12 text-muted">Jan 1 &ndash; today</p>
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

    {{-- YTD Expense --}}
    <div class="col-xl-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-start justify-content-between">
                    <div>
                        <p class="text-muted fw-semibold font-size-13 mb-1">YTD Expense</p>
                        <h4 class="mb-1 text-danger">{{ \App\Helpers\CurrencyHelper::format($ytdSummary['expense'], $currencyCode) }}</h4>
                        <p class="mb-0 font-size-12 text-muted">Jan 1 &ndash; today</p>
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

    {{-- Avg Monthly Income --}}
    <div class="col-xl-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-start justify-content-between">
                    <div>
                        <p class="text-muted fw-semibold font-size-13 mb-1">Avg Monthly Income</p>
                        <h4 class="mb-1 text-info">{{ \App\Helpers\CurrencyHelper::format($avgMonthlySummary['income'], $currencyCode) }}</h4>
                        <p class="mb-0 font-size-12 text-muted">Based on all months</p>
                    </div>
                    <div class="avatar-sm">
                        <span class="avatar-title bg-info-subtle text-info rounded">
                            <i data-feather="activity" class="font-size-20"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Avg Monthly Expense --}}
    <div class="col-xl-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-start justify-content-between">
                    <div>
                        <p class="text-muted fw-semibold font-size-13 mb-1">Avg Monthly Expense</p>
                        <h4 class="mb-1 text-warning">{{ \App\Helpers\CurrencyHelper::format($avgMonthlySummary['expense'], $currencyCode) }}</h4>
                        <p class="mb-0 font-size-12 text-muted">Based on all months</p>
                    </div>
                    <div class="avatar-sm">
                        <span class="avatar-title bg-warning-subtle text-warning rounded">
                            <i data-feather="activity" class="font-size-20"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    {{-- Total Income Records --}}
    <div class="col-xl-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-start justify-content-between">
                    <div>
                        <p class="text-muted fw-semibold font-size-13 mb-1">Total Income Records</p>
                        <h4 class="mb-1 text-success">{{ number_format($txCounts['income_count']) }}</h4>
                        <p class="mb-0 font-size-12 text-muted">All time</p>
                    </div>
                    <div class="avatar-sm">
                        <span class="avatar-title bg-success-subtle text-success rounded">
                            <i data-feather="arrow-up-circle" class="font-size-20"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Total Expense Records --}}
    <div class="col-xl-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-start justify-content-between">
                    <div>
                        <p class="text-muted fw-semibold font-size-13 mb-1">Total Expense Records</p>
                        <h4 class="mb-1 text-danger">{{ number_format($txCounts['expense_count']) }}</h4>
                        <p class="mb-0 font-size-12 text-muted">All time</p>
                    </div>
                    <div class="avatar-sm">
                        <span class="avatar-title bg-danger-subtle text-danger rounded">
                            <i data-feather="arrow-down-circle" class="font-size-20"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Prev Month Income --}}
    <div class="col-xl-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-start justify-content-between">
                    <div>
                        <p class="text-muted fw-semibold font-size-13 mb-1">Prev Month Income</p>
                        <h4 class="mb-1 text-success">{{ \App\Helpers\CurrencyHelper::format($lastMonthSummary['income'] ?? 0, $currencyCode) }}</h4>
                        @if(($lastMonthSummary['income'] ?? 0) > 0 || $summary['income'] > 0)
                        <p class="mb-0 font-size-12">
                            <span class="text-{{ $prevIncomeChange >= 0 ? 'success' : 'danger' }}">
                                <i class="mdi mdi-arrow-{{ $prevIncomeChange >= 0 ? 'up' : 'down' }}-bold"></i>
                                {{ $prevIncomePctRaw }}%
                            </span>
                            <span class="text-muted ms-1">vs this month</span>
                        </p>
                        @endif
                    </div>
                    <div class="avatar-sm">
                        <span class="avatar-title bg-success-subtle text-success rounded">
                            <i data-feather="calendar" class="font-size-20"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Prev Month Expense --}}
    <div class="col-xl-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-start justify-content-between">
                    <div>
                        <p class="text-muted fw-semibold font-size-13 mb-1">Prev Month Expense</p>
                        <h4 class="mb-1 text-danger">{{ \App\Helpers\CurrencyHelper::format($lastMonthSummary['expense'] ?? 0, $currencyCode) }}</h4>
                        @if(($lastMonthSummary['expense'] ?? 0) > 0 || $summary['expense'] > 0)
                        <p class="mb-0 font-size-12">
                            <span class="text-{{ $prevExpenseChange <= 0 ? 'success' : 'danger' }}">
                                <i class="mdi mdi-arrow-{{ $prevExpenseChange <= 0 ? 'down' : 'up' }}-bold"></i>
                                {{ $prevExpensePctRaw }}%
                            </span>
                            <span class="text-muted ms-1">vs this month</span>
                        </p>
                        @endif
                    </div>
                    <div class="avatar-sm">
                        <span class="avatar-title bg-danger-subtle text-danger rounded">
                            <i data-feather="calendar" class="font-size-20"></i>
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
{{-- NET BALANCE TREND + DAILY CASH FLOW --}}
{{-- ============================================================== --}}
<div class="row" x-show="widgets.advanced_charts" x-transition>
    <div class="col-xl-7">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">Monthly Net Balance</h4>
                <span class="badge bg-light text-muted">Last 12 months</span>
            </div>
            <div class="card-body">
                <div id="net-balance-chart" style="height: 340px;">
                    <div class="skeleton skeleton-chart" style="height: 340px;"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-5">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Daily Cash Flow &mdash; {{ now($account->timezone ?? 'UTC')->format('F Y') }}</h4>
            </div>
            <div class="card-body">
                <div id="daily-cashflow-chart" style="height: 340px;">
                    <div class="skeleton skeleton-chart" style="height: 340px;"></div>
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
{{-- TOP INCOME SOURCES --}}
{{-- ============================================================== --}}
<div class="row" x-show="widgets.income_cats" x-transition>
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0"><i data-feather="bar-chart" class="icon-xs me-1"></i> Top Income Sources &mdash; This Month</h4>
                <a href="{{ route('reports.index') }}" class="btn btn-sm btn-outline-primary">Full Report</a>
            </div>
            <div class="card-body">
                <div id="top-income-categories-chart" style="height: 280px;">
                    <div class="skeleton skeleton-chart" style="height: 280px;"></div>
                </div>
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
{{-- THIS MONTH VS LAST MONTH COMPARISON --}}
{{-- ============================================================== --}}
<div class="row" x-show="widgets.comparison" x-transition>
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0"><i data-feather="git-compare" class="icon-xs me-1"></i> This Month vs Last Month</h4>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    {{-- Income --}}
                    <div class="col-md-3 border-end pb-3 pb-md-0">
                        <p class="text-muted font-size-12 mb-2 fw-semibold text-uppercase">Income</p>
                        <h5 class="text-success mb-1">{{ \App\Helpers\CurrencyHelper::format($summary['income'], $currencyCode) }}</h5>
                        <p class="text-muted font-size-12 mb-2">This Month</p>
                        <h6 class="mb-1">{{ \App\Helpers\CurrencyHelper::format($lastMonthSummary['income'] ?? 0, $currencyCode) }}</h6>
                        <p class="text-muted font-size-12 mb-2">Last Month</p>
                        @if($incomeDiff != 0)
                        <span class="badge bg-{{ $incomeDiff >= 0 ? 'success' : 'danger' }}-subtle text-{{ $incomeDiff >= 0 ? 'success' : 'danger' }} font-size-11">
                            <i class="mdi mdi-arrow-{{ $incomeDiff >= 0 ? 'up' : 'down' }}-bold"></i>
                            {{ abs($incomePct) }}%
                        </span>
                        @endif
                    </div>
                    {{-- Expense --}}
                    <div class="col-md-3 border-end pb-3 pb-md-0">
                        <p class="text-muted font-size-12 mb-2 fw-semibold text-uppercase">Expense</p>
                        <h5 class="text-danger mb-1">{{ \App\Helpers\CurrencyHelper::format($summary['expense'], $currencyCode) }}</h5>
                        <p class="text-muted font-size-12 mb-2">This Month</p>
                        <h6 class="mb-1">{{ \App\Helpers\CurrencyHelper::format($lastMonthSummary['expense'] ?? 0, $currencyCode) }}</h6>
                        <p class="text-muted font-size-12 mb-2">Last Month</p>
                        @if($expenseDiff != 0)
                        <span class="badge bg-{{ $expenseDiff <= 0 ? 'success' : 'danger' }}-subtle text-{{ $expenseDiff <= 0 ? 'success' : 'danger' }} font-size-11">
                            <i class="mdi mdi-arrow-{{ $expenseDiff <= 0 ? 'down' : 'up' }}-bold"></i>
                            {{ abs($expensePct) }}%
                        </span>
                        @endif
                    </div>
                    {{-- Net --}}
                    <div class="col-md-3 border-end pb-3 pb-md-0">
                        <p class="text-muted font-size-12 mb-2 fw-semibold text-uppercase">Net</p>
                        <h5 class="{{ $summary['net'] >= 0 ? 'text-success' : 'text-danger' }} mb-1">{{ \App\Helpers\CurrencyHelper::format($summary['net'], $currencyCode) }}</h5>
                        <p class="text-muted font-size-12 mb-2">This Month</p>
                        <h6 class="mb-1">{{ \App\Helpers\CurrencyHelper::format($lastMonthSummary['net'] ?? 0, $currencyCode) }}</h6>
                        <p class="text-muted font-size-12 mb-2">Last Month</p>
                        @if($netDiff != 0)
                        <span class="badge bg-{{ $netDiff >= 0 ? 'success' : 'danger' }}-subtle text-{{ $netDiff >= 0 ? 'success' : 'danger' }} font-size-11">
                            <i class="mdi mdi-arrow-{{ $netDiff >= 0 ? 'up' : 'down' }}-bold"></i>
                            {{ \App\Helpers\CurrencyHelper::format(abs($netDiff), $currencyCode) }}
                        </span>
                        @endif
                    </div>
                    {{-- Transactions --}}
                    <div class="col-md-3">
                        <p class="text-muted font-size-12 mb-2 fw-semibold text-uppercase">Transactions</p>
                        <h5 class="text-primary mb-1">{{ $transactionCount ?? 0 }}</h5>
                        <p class="text-muted font-size-12 mb-2">This Month</p>
                        <h6 class="mb-1">{{ $lastMonthTxCount ?? 0 }}</h6>
                        <p class="text-muted font-size-12 mb-2">Last Month</p>
                        @if($txCountDiff != 0)
                        <span class="badge bg-{{ $txCountDiff >= 0 ? 'success' : 'danger' }}-subtle text-{{ $txCountDiff >= 0 ? 'success' : 'danger' }} font-size-11">
                            <i class="mdi mdi-arrow-{{ $txCountDiff >= 0 ? 'up' : 'down' }}-bold"></i>
                            {{ abs($txCountDiff) }} txns
                        </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ============================================================== --}}
{{-- FINANCIAL INSIGHTS --}}
{{-- ============================================================== --}}
<div class="row" x-show="widgets.insights" x-transition>
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0"><i data-feather="zap" class="icon-xs me-1"></i> Financial Insights</h4>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    {{-- Best Month --}}
                    @if(!empty($bestWorstMonths['best']))
                    <div class="col-md-4">
                        <div class="border rounded p-3 h-100">
                            <p class="text-muted font-size-12 mb-2">
                                <i data-feather="award" class="icon-xs me-1 text-success"></i>
                                <strong>Best Month</strong> <span class="text-muted">(last 12 months)</span>
                            </p>
                            <h5 class="text-success mb-1">{{ $bestWorstMonths['best']['month'] }}</h5>
                            <p class="mb-1 font-size-13">
                                Net: <strong class="text-success">{{ \App\Helpers\CurrencyHelper::format($bestWorstMonths['best']['net'], $currencyCode) }}</strong>
                            </p>
                            <p class="mb-0 font-size-12 text-muted">
                                In: {{ \App\Helpers\CurrencyHelper::format($bestWorstMonths['best']['income'], $currencyCode) }} &nbsp;/&nbsp;
                                Out: {{ \App\Helpers\CurrencyHelper::format($bestWorstMonths['best']['expense'], $currencyCode) }}
                            </p>
                        </div>
                    </div>
                    @endif

                    {{-- Worst Month --}}
                    @if(!empty($bestWorstMonths['worst']))
                    <div class="col-md-4">
                        <div class="border rounded p-3 h-100">
                            <p class="text-muted font-size-12 mb-2">
                                <i data-feather="alert-triangle" class="icon-xs me-1 text-danger"></i>
                                <strong>Worst Month</strong> <span class="text-muted">(last 12 months)</span>
                            </p>
                            <h5 class="text-danger mb-1">{{ $bestWorstMonths['worst']['month'] }}</h5>
                            <p class="mb-1 font-size-13">
                                Net: <strong class="text-danger">{{ \App\Helpers\CurrencyHelper::format($bestWorstMonths['worst']['net'], $currencyCode) }}</strong>
                            </p>
                            <p class="mb-0 font-size-12 text-muted">
                                In: {{ \App\Helpers\CurrencyHelper::format($bestWorstMonths['worst']['income'], $currencyCode) }} &nbsp;/&nbsp;
                                Out: {{ \App\Helpers\CurrencyHelper::format($bestWorstMonths['worst']['expense'], $currencyCode) }}
                            </p>
                        </div>
                    </div>
                    @endif

                    {{-- Largest Transactions --}}
                    <div class="col-md-4">
                        <div class="border rounded p-3 h-100">
                            <p class="text-muted font-size-12 mb-2">
                                <i data-feather="maximize-2" class="icon-xs me-1 text-info"></i>
                                <strong>Largest Transactions</strong> <span class="text-muted">(all time)</span>
                            </p>
                            @if(!empty($largestTxns['largest_income']))
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="font-size-12 text-muted">Largest Income</span>
                                <span class="text-success fw-semibold font-size-13">{{ \App\Helpers\CurrencyHelper::format($largestTxns['largest_income']['amount'], $currencyCode) }}</span>
                            </div>
                            <p class="font-size-11 text-muted mb-2">{{ $largestTxns['largest_income']['category'] }} &middot; {{ $largestTxns['largest_income']['occurred_at'] }}</p>
                            @endif
                            @if(!empty($largestTxns['largest_expense']))
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="font-size-12 text-muted">Largest Expense</span>
                                <span class="text-danger fw-semibold font-size-13">{{ \App\Helpers\CurrencyHelper::format($largestTxns['largest_expense']['amount'], $currencyCode) }}</span>
                            </div>
                            <p class="font-size-11 text-muted mb-0">{{ $largestTxns['largest_expense']['category'] }} &middot; {{ $largestTxns['largest_expense']['occurred_at'] }}</p>
                            @endif
                            @if(empty($largestTxns['largest_income']) && empty($largestTxns['largest_expense']))
                            <p class="text-muted mb-0 font-size-13">No transactions yet.</p>
                            @endif
                        </div>
                    </div>
                </div>
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
{{-- MONTHLY SUMMARY TABLE (sortable) --}}
{{-- ============================================================== --}}
<div class="row" x-show="widgets.monthly_table" x-transition>
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0"><i data-feather="list" class="icon-xs me-1"></i> Monthly Summary</h4>
                <span class="badge bg-light text-muted">Last 12 months</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive"
                     x-data="sortableTable({
                        rows: @js(array_values(array_map(fn($r) => [
                            'month_label' => $r['month'],
                            'month_sort'  => $r['month'],
                            'income'      => (float) $r['income'],
                            'expense'     => (float) $r['expense'],
                            'net'         => (float) $r['net'],
                        ], array_reverse($last12Months)))),
                        defaultSort: 'month_sort',
                        defaultDir: 'desc'
                     })">
                    <table class="table table-sm table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3" role="button" @click="sortBy('month_sort')">
                                    Month <span class="text-muted small" x-text="indicator('month_sort')"></span>
                                </th>
                                <th class="text-end" role="button" @click="sortBy('income')">
                                    Income <span class="text-muted small" x-text="indicator('income')"></span>
                                </th>
                                <th class="text-end" role="button" @click="sortBy('expense')">
                                    Expense <span class="text-muted small" x-text="indicator('expense')"></span>
                                </th>
                                <th class="text-end pe-3" role="button" @click="sortBy('net')">
                                    Net <span class="text-muted small" x-text="indicator('net')"></span>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="row in sorted" :key="row.month_sort">
                                <tr>
                                    <td class="ps-3 fw-medium" x-text="row.month_label"></td>
                                    <td class="text-end text-success" x-text="formatMoney(row.income)"></td>
                                    <td class="text-end text-danger" x-text="formatMoney(row.expense)"></td>
                                    <td class="text-end pe-3 fw-semibold"
                                        :class="row.net >= 0 ? 'text-success' : 'text-danger'"
                                        x-text="(row.net >= 0 ? '+' : '') + formatMoney(row.net)"></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ============================================================== --}}
{{-- PER-ACCOUNT (WALLET) TABLE (sortable) --}}
{{-- ============================================================== --}}
<div class="row" x-show="widgets.accounts_table" x-transition>
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0"><i data-feather="layers" class="icon-xs me-1"></i> Accounts (Wallets)</h4>
                <a href="{{ route('wallets.index') }}" class="btn btn-sm btn-outline-primary">Manage</a>
            </div>
            <div class="card-body p-0">
                @if(empty($walletStats))
                    <div class="text-center py-4">
                        <p class="text-muted mb-2">No wallets yet.</p>
                        <a href="{{ route('wallets.create') }}" class="btn btn-sm btn-primary">Add wallet</a>
                    </div>
                @else
                <div class="table-responsive"
                     x-data="sortableTable({
                        rows: @js($walletStats),
                        defaultSort: 'balance',
                        defaultDir: 'desc'
                     })">
                    <table class="table table-sm table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3" role="button" @click="sortBy('name')">
                                    Account <span class="text-muted small" x-text="indicator('name')"></span>
                                </th>
                                <th role="button" @click="sortBy('type')">
                                    Type <span class="text-muted small" x-text="indicator('type')"></span>
                                </th>
                                <th class="text-end" role="button" @click="sortBy('opening_balance')">
                                    Opening <span class="text-muted small" x-text="indicator('opening_balance')"></span>
                                </th>
                                <th class="text-end" role="button" @click="sortBy('income')">
                                    Income <span class="text-muted small" x-text="indicator('income')"></span>
                                </th>
                                <th class="text-end" role="button" @click="sortBy('expense')">
                                    Expense <span class="text-muted small" x-text="indicator('expense')"></span>
                                </th>
                                <th class="text-end" role="button" @click="sortBy('balance')">
                                    Balance <span class="text-muted small" x-text="indicator('balance')"></span>
                                </th>
                                <th class="text-end pe-3" role="button" @click="sortBy('transaction_count')">
                                    Txns <span class="text-muted small" x-text="indicator('transaction_count')"></span>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="row in sorted" :key="row.id">
                                <tr>
                                    <td class="ps-3 fw-medium" x-text="row.name"></td>
                                    <td><span class="badge bg-light text-muted text-capitalize" x-text="row.type"></span></td>
                                    <td class="text-end" x-text="formatMoney(row.opening_balance)"></td>
                                    <td class="text-end text-success" x-text="formatMoney(row.income)"></td>
                                    <td class="text-end text-danger" x-text="formatMoney(row.expense)"></td>
                                    <td class="text-end fw-semibold"
                                        :class="row.balance >= 0 ? 'text-success' : 'text-danger'"
                                        x-text="formatMoney(row.balance)"></td>
                                    <td class="text-end pe-3" x-text="row.transaction_count"></td>
                                </tr>
                            </template>
                        </tbody>
                        <tfoot class="table-light">
                            <tr class="fw-semibold">
                                <td class="ps-3">Total</td>
                                <td></td>
                                <td class="text-end" x-text="formatMoney(totalOf('opening_balance'))"></td>
                                <td class="text-end text-success" x-text="formatMoney(totalOf('income'))"></td>
                                <td class="text-end text-danger" x-text="formatMoney(totalOf('expense'))"></td>
                                <td class="text-end" :class="totalOf('balance') >= 0 ? 'text-success' : 'text-danger'" x-text="formatMoney(totalOf('balance'))"></td>
                                <td class="text-end pe-3" x-text="totalOf('transaction_count')"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                @endif
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
        alltime_totals: true,
        cash_match: true,
        monthly: true,
        ytd_stats: true,
        ratio_chart: true,
        advanced_charts: true,
        breakdown_charts: true,
        income_cats: true,
        tables: true,
        comparison: true,
        insights: true,
        quick_actions: true,
        alltime: true,
        monthly_table: true,
        accounts_table: true,
        breakdown_pie: true
    };
    return {
        widgets: Object.assign({}, defaults),
        widgetList: [
            { key: 'alltime_totals',   label: 'All-Time Totals' },
            { key: 'cash_match',       label: 'Cash Match (Reconcile)' },
            { key: 'monthly',          label: 'Key Metrics' },
            { key: 'ytd_stats',        label: 'YTD & Averages' },
            { key: 'ratio_chart',      label: 'Trend Charts' },
            { key: 'advanced_charts',  label: 'Net Trend & Daily Flow' },
            { key: 'breakdown_charts', label: 'Wallets & Top Expenses' },
            { key: 'income_cats',      label: 'Top Income Sources' },
            { key: 'tables',           label: 'Budgets & Recent Transactions' },
            { key: 'comparison',       label: 'Month vs Month Comparison' },
            { key: 'insights',         label: 'Financial Insights' },
            { key: 'quick_actions',    label: 'Quick Actions' },
            { key: 'alltime',          label: 'All-Time Summary' },
            { key: 'monthly_table',    label: 'Monthly Summary Table' },
            { key: 'accounts_table',   label: 'Accounts (Wallets) Table' },
            { key: 'breakdown_pie',    label: 'Category & Source Charts' }
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

function sortableTable(config) {
    return {
        rows: config.rows || [],
        sortKey: config.defaultSort || null,
        sortDir: config.defaultDir === 'asc' ? 'asc' : 'desc',
        get sorted() {
            if (!this.sortKey) return this.rows;
            var key = this.sortKey;
            var dir = this.sortDir === 'asc' ? 1 : -1;
            var copy = this.rows.slice();
            copy.sort(function(a, b) {
                var av = a[key];
                var bv = b[key];
                if (av === bv) return 0;
                if (typeof av === 'number' && typeof bv === 'number') {
                    return (av - bv) * dir;
                }
                return String(av).localeCompare(String(bv)) * dir;
            });
            return copy;
        },
        sortBy: function(key) {
            if (this.sortKey === key) {
                this.sortDir = this.sortDir === 'asc' ? 'desc' : 'asc';
            } else {
                this.sortKey = key;
                this.sortDir = 'desc';
            }
        },
        indicator: function(key) {
            if (this.sortKey !== key) return '';
            return this.sortDir === 'asc' ? '▲' : '▼';
        },
        formatMoney: function(val) {
            var num = Number(val) || 0;
            return num.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' {{ $currencyCode }}';
        },
        totalOf: function(key) {
            return this.rows.reduce(function(sum, r) { return sum + (Number(r[key]) || 0); }, 0);
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
            colors: [@foreach($topCategories as $cat)'{{ $cat["color"] }}',@endforeach],
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

    // ── Bar Chart: Monthly Net Balance (distributed colors per bar) ──
    try {
    @if($hasMonthlyData)
    var netBarEl = document.querySelector('#net-balance-chart');
    if (netBarEl) {
        netBarEl.innerHTML = '';
        new ApexCharts(netBarEl, {
            series: [{ name: 'Net', data: [{{ implode(',', array_column($monthlyData, 'net')) }}] }],
            chart: { type: 'bar', height: 340, toolbar: { show: false }, fontFamily: 'inherit' },
            plotOptions: { bar: { distributed: true, borderRadius: 4, columnWidth: '55%' } },
            colors: [{!! implode(',', array_map(fn($d) => "'" . ($d['net'] >= 0 ? '#0ab39c' : '#f06548') . "'", $monthlyData)) !!}],
            dataLabels: { enabled: false },
            legend: { show: false },
            xaxis: {
                categories: [@foreach($monthlyData as $d)'{{ $d["month"] }}',@endforeach],
                labels: { style: { fontSize: '11px' } }
            },
            yaxis: {
                labels: {
                    formatter: function(val) { return val >= 1000000 ? (val/1000000).toFixed(1)+'M' : val >= 1000 ? (val/1000).toFixed(1)+'K' : val.toFixed(0); }
                }
            },
            tooltip: { y: { formatter: function(val) { return val.toLocaleString() + ' {{ $currencyCode }}'; } } },
            grid: { borderColor: chartTheme.grid }
        }).render();
    }
    @else
    var netBarEl = document.getElementById('net-balance-chart');
    if (netBarEl) netBarEl.innerHTML = '<p class="text-muted text-center py-5">No data available</p>';
    @endif
    } catch(e) { console.warn('Net balance chart error:', e); }

    // ── Area Chart: Daily Cash Flow ──
    try {
    @php $hasDailyData = !empty($dailyTrend); @endphp
    @if($hasDailyData)
    var dailyEl = document.querySelector('#daily-cashflow-chart');
    if (dailyEl) {
        dailyEl.innerHTML = '';
        new ApexCharts(dailyEl, {
            series: [{
                name: 'Income',
                data: [{{ implode(',', array_column($dailyTrend, 'income')) }}]
            }, {
                name: 'Expense',
                data: [{{ implode(',', array_column($dailyTrend, 'expense')) }}]
            }],
            chart: { type: 'area', height: 340, toolbar: { show: false }, fontFamily: 'inherit' },
            colors: [chartTheme.income, chartTheme.expense],
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth', width: [2, 2] },
            fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.25, opacityTo: 0.05 } },
            xaxis: {
                categories: [@foreach($dailyTrend as $d)'{{ $d["day"] }}',@endforeach],
                labels: { style: { fontSize: '10px' }, rotate: -45 },
                tickAmount: 10
            },
            yaxis: {
                labels: {
                    formatter: function(val) { return val >= 1000000 ? (val/1000000).toFixed(1)+'M' : val >= 1000 ? (val/1000).toFixed(1)+'K' : val.toFixed(0); }
                }
            },
            legend: { position: 'top', horizontalAlign: 'right' },
            tooltip: { y: { formatter: function(val) { return val.toLocaleString() + ' {{ $currencyCode }}'; } } },
            grid: { borderColor: chartTheme.grid }
        }).render();
    }
    @else
    var dailyEl = document.getElementById('daily-cashflow-chart');
    if (dailyEl) dailyEl.innerHTML = '<p class="text-muted text-center py-5">No data this month</p>';
    @endif
    } catch(e) { console.warn('Daily cashflow chart error:', e); }

    // ── Horizontal Bar Chart: Top Income Sources (by wallet) ──
    try {
    @php $hasTopIncomeWallets = !empty($topIncomeWallets) && count($topIncomeWallets) > 0; @endphp
    @if($hasTopIncomeWallets)
    var incomeCatEl = document.querySelector('#top-income-categories-chart');
    if (incomeCatEl) {
        incomeCatEl.innerHTML = '';
        new ApexCharts(incomeCatEl, {
            series: [{ name: 'Income', data: [{{ implode(',', array_column($topIncomeWallets, 'total')) }}] }],
            chart: { type: 'bar', height: 280, toolbar: { show: false }, fontFamily: 'inherit' },
            plotOptions: { bar: { horizontal: true, borderRadius: 4, barHeight: '55%' } },
            colors: [chartTheme.income],
            dataLabels: { enabled: false },
            xaxis: {
                labels: {
                    formatter: function(val) { return val >= 1000000 ? (val/1000000).toFixed(1)+'M' : val >= 1000 ? (val/1000).toFixed(1)+'K' : val.toFixed(0); }
                }
            },
            yaxis: {
                categories: [@foreach($topIncomeWallets as $w)'{{ addslashes($w["label"]) }}',@endforeach]
            },
            tooltip: { y: { formatter: function(val) { return val.toLocaleString() + ' {{ $currencyCode }}'; } } },
            grid: { borderColor: chartTheme.grid }
        }).render();
    }
    @else
    var incomeCatEl = document.getElementById('top-income-categories-chart');
    if (incomeCatEl) incomeCatEl.innerHTML = '<p class="text-muted text-center py-5">No income data this month</p>';
    @endif
    } catch(e) { console.warn('Income sources chart error:', e); }
};
</script>
@endpush
@endsection
