@extends('layouts.app')

@section('title', 'Search Results')

@php
    $currencyCode = $__currencyCode;
    $account = $__account;
@endphp

@section('breadcrumbs')
    <li class="breadcrumb-item active">Search</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Search Results</h4>
        </div>
        @if($query)
            <p class="text-muted mb-4">Showing results for "<strong>{{ $query }}</strong>"</p>
        @else
            <p class="text-muted mb-4">Enter a search term to find transactions, income sources, expense types, and tags.</p>
        @endif
    </div>
</div>

<!-- Search Form -->
<div class="row mb-3">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ route('search') }}" class="d-flex gap-2">
                    <input type="text" name="q" class="form-control form-control-lg" placeholder="Search transactions, wallets, categories, tags..." value="{{ $query }}" autofocus>
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i data-feather="search" class="align-middle"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@if($query && !$hasResults)
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center py-5">
                    <i data-feather="search" class="icon-lg text-muted mb-3" style="width: 48px; height: 48px;"></i>
                    <h5 class="text-muted">No results found</h5>
                    <p class="text-muted mb-0">Try a different search term or check the spelling.</p>
                </div>
            </div>
        </div>
    </div>
@endif

@if($transactions->isNotEmpty())
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i data-feather="repeat" class="align-middle me-2"></i>
                    Transactions <span class="badge bg-primary">{{ $transactions->count() }}</span>
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-nowrap mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Amount</th>
                                <th>Income Source</th>
                                <th>Expense Type</th>
                                <th>Note</th>
                                <th>Tags</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transactions as $transaction)
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
                                <td>{{ Str::limit($transaction->note, 40) }}</td>
                                <td>
                                    @foreach($transaction->tags as $tag)
                                        <span class="badge" style="background-color: {{ $tag->color }}">{{ $tag->name }}</span>
                                    @endforeach
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('transactions.edit', $transaction) }}" class="btn btn-sm btn-primary">
                                        <i data-feather="edit-2" class="icon-xs"></i> Edit
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($transactions->count() >= 20)
                    <div class="text-center mt-3">
                        <a href="{{ route('transactions.index', ['search' => $query]) }}" class="btn btn-outline-primary btn-sm">
                            View all matching transactions <i data-feather="arrow-right" class="icon-xs ms-1"></i>
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endif

@if($wallets->isNotEmpty())
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i data-feather="trending-up" class="align-middle me-2"></i>
                    Income Sources <span class="badge bg-success">{{ $wallets->count() }}</span>
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    @foreach($wallets as $wallet)
                    <div class="col-md-4 col-lg-3">
                        <a href="{{ route('wallets.edit', $wallet) }}" class="text-decoration-none">
                            <div class="card border mb-0 h-100">
                                <div class="card-body p-3">
                                    <h6 class="mb-1">{{ $wallet->name }}</h6>
                                    <span class="badge bg-{{ $wallet->is_active ? 'success' : 'secondary' }}">
                                        {{ $wallet->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>
                            </div>
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endif

@if($categories->isNotEmpty())
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i data-feather="tag" class="align-middle me-2"></i>
                    Expense Types <span class="badge bg-danger">{{ $categories->count() }}</span>
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    @foreach($categories as $category)
                    <div class="col-md-4 col-lg-3">
                        <a href="{{ route('categories.edit', $category) }}" class="text-decoration-none">
                            <div class="card border mb-0 h-100">
                                <div class="card-body p-3">
                                    <h6 class="mb-1">{{ $category->name }}</h6>
                                    <span class="badge bg-{{ $category->is_active ? 'success' : 'secondary' }}">
                                        {{ $category->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>
                            </div>
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endif

@if($tags->isNotEmpty())
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i data-feather="bookmark" class="align-middle me-2"></i>
                    Tags <span class="badge bg-info">{{ $tags->count() }}</span>
                </h5>
            </div>
            <div class="card-body">
                <div class="d-flex flex-wrap gap-2">
                    @foreach($tags as $tag)
                        <a href="{{ route('transactions.index', ['tag_id' => $tag->id]) }}" class="text-decoration-none">
                            <span class="badge fs-6 py-2 px-3" style="background-color: {{ $tag->color }}">
                                {{ $tag->name }}
                            </span>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endif

@endsection
