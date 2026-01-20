@extends('layouts.app')

@section('title', 'Transactions')

@php
    $accountContext = app(\App\Services\AccountContext::class);
    $account = $accountContext->account();
    $currencyCode = $account?->currency_code ?? 'IQD';
@endphp

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Transactions</h4>
            <div class="page-title-right">
                <a href="{{ route('transactions.create') }}" class="btn btn-primary">
                    <i data-feather="plus" class="align-middle me-1"></i> Add Transaction
                </a>
            </div>
        </div>
        <p class="text-muted mb-4">View and manage all your income and expense transactions. Filter by type, date range, income source, or expense type.</p>
    </div>
</div>

<div class="row mb-3">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ route('transactions.index') }}" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Type</label>
                        <select name="type" class="form-select">
                            <option value="">All Types</option>
                            <option value="income" {{ request('type') === 'income' ? 'selected' : '' }}>Income</option>
                            <option value="expense" {{ request('type') === 'expense' ? 'selected' : '' }}>Expense</option>
                            <option value="transfer" {{ request('type') === 'transfer' ? 'selected' : '' }}>Transfer</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Income Source</label>
                        <select name="wallet_id" class="form-select">
                            <option value="">All Sources</option>
                            @foreach(\App\Models\Wallet::forAccount($account->id)->active()->get() as $wallet)
                                <option value="{{ $wallet->id }}" {{ request('wallet_id') == $wallet->id ? 'selected' : '' }}>{{ $wallet->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Expense Type</label>
                        <select name="category_id" class="form-select">
                            <option value="">All Types</option>
                            @foreach(\App\Models\Category::forAccount($account->id)->active()->get() as $category)
                                <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Search</label>
                        <input type="text" name="search" class="form-control" placeholder="Search notes..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Start Date</label>
                        <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">End Date</label>
                        <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i data-feather="filter" class="align-middle me-1"></i> Apply Filters
                        </button>
                        <a href="{{ route('transactions.index') }}" class="btn btn-secondary">
                            <i data-feather="x" class="align-middle me-1"></i> Clear
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-nowrap table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>
                                    <a href="{{ route('transactions.index', array_merge(request()->all(), ['sort' => 'occurred_at', 'direction' => request('sort') == 'occurred_at' && request('direction') == 'desc' ? 'asc' : 'desc'])) }}" class="text-dark text-decoration-none">
                                        Date
                                        @if(request('sort') == 'occurred_at')
                                            <i data-feather="{{ request('direction') == 'asc' ? 'arrow-up' : 'arrow-down' }}" class="icon-xs"></i>
                                        @endif
                                    </a>
                                </th>
                                <th>
                                    <a href="{{ route('transactions.index', array_merge(request()->all(), ['sort' => 'type', 'direction' => request('sort') == 'type' && request('direction') == 'asc' ? 'desc' : 'asc'])) }}" class="text-dark text-decoration-none">
                                        Type
                                        @if(request('sort') == 'type')
                                            <i data-feather="{{ request('direction') == 'asc' ? 'arrow-up' : 'arrow-down' }}" class="icon-xs"></i>
                                        @endif
                                    </a>
                                </th>
                                <th>
                                    <a href="{{ route('transactions.index', array_merge(request()->all(), ['sort' => 'amount', 'direction' => request('sort') == 'amount' && request('direction') == 'asc' ? 'desc' : 'asc'])) }}" class="text-dark text-decoration-none">
                                        Amount
                                        @if(request('sort') == 'amount')
                                            <i data-feather="{{ request('direction') == 'asc' ? 'arrow-up' : 'arrow-down' }}" class="icon-xs"></i>
                                        @endif
                                    </a>
                                </th>
                                <th>Income Source</th>
                                <th>Expense Type</th>
                                <th>Note</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transactions as $transaction)
                                <tr>
                                    <td>{{ $transaction->occurred_at->format('M d, Y') }}</td>
                                    <td>
                                        <span class="badge bg-{{ $transaction->type === 'income' ? 'success' : ($transaction->type === 'expense' ? 'danger' : 'info') }}">
                                            {{ ucfirst($transaction->type) }}
                                        </span>
                                    </td>
                                    <td>
                                        <strong class="{{ $transaction->type === 'income' ? 'text-success' : 'text-danger' }}">
                                            {{ \App\Helpers\CurrencyHelper::format($transaction->amount, $currencyCode) }}
                                        </strong>
                                    </td>
                                    <td>{{ $transaction->wallet->name ?? 'N/A' }}</td>
                                    <td>{{ $transaction->category->name ?? 'N/A' }}</td>
                                    <td>{{ Str::limit($transaction->note, 30) }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('transactions.edit', $transaction) }}" class="btn btn-sm btn-primary">
                                            <i data-feather="edit-2" class="icon-xs"></i> Edit
                                        </a>
                                        <form action="{{ route('transactions.destroy', $transaction) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
                                                <i data-feather="trash-2" class="icon-xs"></i> Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <div class="text-muted">
                                            <i data-feather="arrow-left-right" class="icon-lg mb-2"></i>
                                            <p class="mb-2">No transactions found.</p>
                                            <a href="{{ route('transactions.create') }}" class="btn btn-primary">Add Your First Transaction</a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($transactions->hasPages())
                    <div class="d-flex justify-content-end mt-3">
                        {{ $transactions->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
