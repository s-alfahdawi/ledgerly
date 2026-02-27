@extends('layouts.app')

@section('title', 'Reports')

@php
    $currencyCode = $__currencyCode;
    $account = $__account;
@endphp

@section('breadcrumbs')
    <li class="breadcrumb-item active">Reports</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Reports</h4>
        </div>
        <p class="text-muted mb-4">Analyze your financial data with detailed reports. Filter by date range to see income, expenses, and category breakdowns.</p>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Report Filters</h4>
            </div>
            <div class="card-body">
                {{-- Quick date presets --}}
                @php
                    $tz = $account->timezone ?? 'UTC';
                    $today = now($tz);
                    $presets = [
                        'This Month' => [$today->copy()->startOfMonth()->toDateString(), $today->copy()->endOfMonth()->toDateString()],
                        'Last Month' => [$today->copy()->subMonth()->startOfMonth()->toDateString(), $today->copy()->subMonth()->endOfMonth()->toDateString()],
                        'This Quarter' => [$today->copy()->firstOfQuarter()->toDateString(), $today->copy()->lastOfQuarter()->toDateString()],
                        'This Year' => [$today->copy()->startOfYear()->toDateString(), $today->copy()->endOfYear()->toDateString()],
                        'Last 30 Days' => [$today->copy()->subDays(30)->toDateString(), $today->toDateString()],
                        'Last 90 Days' => [$today->copy()->subDays(90)->toDateString(), $today->toDateString()],
                    ];
                @endphp
                <div class="mb-3">
                    <label class="form-label d-block">Quick Range</label>
                    @foreach($presets as $label => [$presetStart, $presetEnd])
                        <a href="{{ route('reports.index', ['start_date' => $presetStart, 'end_date' => $presetEnd, 'transaction_type' => request('transaction_type')]) }}"
                           class="btn btn-sm {{ $startDate === $presetStart && $endDate === $presetEnd ? 'btn-primary' : 'btn-outline-primary' }} mb-1">
                            {{ $label }}
                        </a>
                    @endforeach
                </div>
                <form method="GET" action="{{ route('reports.index') }}">
                    <div class="row">
                        <div class="col-md-3">
                            <label class="form-label">Start Date</label>
                            <input type="date" name="start_date" class="form-control" value="{{ $startDate }}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">End Date</label>
                            <input type="date" name="end_date" class="form-control" value="{{ $endDate }}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Transaction Type</label>
                            <select name="transaction_type" class="form-select">
                                <option value="">All Types</option>
                                <option value="income" {{ request('transaction_type') === 'income' ? 'selected' : '' }}>Income Only</option>
                                <option value="expense" {{ request('transaction_type') === 'expense' ? 'selected' : '' }}>Expense Only</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">&nbsp;</label>
                            <button type="submit" class="btn btn-primary d-block">
                                <i data-feather="filter" class="align-middle me-1"></i> Apply Filters
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Expense Type Breakdown</h4>
            </div>
            <div class="card-body">
                @if(!empty($categoryBreakdown) && count($categoryBreakdown) > 0)
                    <div class="table-responsive">
                        <table class="table table-nowrap mb-0">
                            <thead>
                                <tr>
                                    <th>Expense Type</th>
                                    <th class="text-end">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($categoryBreakdown as $item)
                                    <tr>
                                        <td>{{ $item['category'] }}</td>
                                        <td class="text-end">
                                            <strong>{{ \App\Helpers\CurrencyHelper::format($item['total'], $currencyCode) }}</strong>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted mb-0">No expenses found in this period.</p>
                @endif
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Income Source Breakdown</h4>
            </div>
            <div class="card-body">
                @if(!empty($walletBreakdown) && count($walletBreakdown) > 0)
                    <div class="table-responsive">
                        <table class="table table-nowrap mb-0">
                            <thead>
                                <tr>
                                    <th>Income Source</th>
                                    <th class="text-end">Balance</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($walletBreakdown as $item)
                                    <tr>
                                        <td>{{ $item['wallet'] }}</td>
                                        <td class="text-end">
                                            <strong class="{{ $item['balance'] >= 0 ? 'text-success' : 'text-danger' }}">
                                                {{ \App\Helpers\CurrencyHelper::format($item['balance'], $currencyCode) }}
                                            </strong>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted mb-0">No income sources found in this period.</p>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex gap-2">
                    <a href="{{ route('reports.export.statement', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="btn btn-primary">
                        <i data-feather="download" class="align-middle me-1"></i> Export PDF Report
                    </a>
                    <a href="{{ route('reports.export.csv', ['start_date' => $startDate, 'end_date' => $endDate, 'transaction_type' => request('transaction_type')]) }}" class="btn btn-success">
                        <i data-feather="file-text" class="align-middle me-1"></i> Export CSV
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
