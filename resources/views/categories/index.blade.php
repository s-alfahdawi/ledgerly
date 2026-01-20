@extends('layouts.app')

@section('title', 'Expense Types')

@php
    $accountContext = app(\App\Services\AccountContext::class);
    $account = $accountContext->account();
    $currencyCode = $account?->currency_code ?? 'IQD';
@endphp

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Expense Types</h4>
            <div class="page-title-right">
                <a href="{{ route('categories.create') }}" class="btn btn-primary">
                    <i data-feather="plus" class="align-middle me-1"></i> Add Expense Type
                </a>
            </div>
        </div>
        <p class="text-muted mb-4">Organize your expenses by type such as rent, food, gas, utilities, etc. This helps you track where your money goes.</p>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h4 class="card-title mb-0">All Expense Types</h4>
                    </div>
                    <div class="col-md-6">
                        <form method="GET" action="{{ route('categories.index') }}" class="d-flex gap-2">
                            <input type="text" name="search" class="form-control" placeholder="Search by name..." value="{{ request('search') }}">
                            <button type="submit" class="btn btn-primary"><i data-feather="search"></i></button>
                            @if(request('search'))
                                <a href="{{ route('categories.index') }}" class="btn btn-secondary"><i data-feather="x"></i></a>
                            @endif
                        </form>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-nowrap table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>
                                    <a href="{{ route('categories.index', array_merge(request()->all(), ['sort' => 'name', 'direction' => request('sort') == 'name' && request('direction') == 'asc' ? 'desc' : 'asc'])) }}" class="text-dark text-decoration-none">
                                        Name
                                        @if(request('sort') == 'name')
                                            <i data-feather="{{ request('direction') == 'asc' ? 'arrow-up' : 'arrow-down' }}" class="icon-xs"></i>
                                        @endif
                                    </a>
                                </th>
                                <th>
                                    <a href="{{ route('categories.index', array_merge(request()->all(), ['sort' => 'type', 'direction' => request('sort') == 'type' && request('direction') == 'asc' ? 'desc' : 'asc'])) }}" class="text-dark text-decoration-none">
                                        Type
                                        @if(request('sort') == 'type')
                                            <i data-feather="{{ request('direction') == 'asc' ? 'arrow-up' : 'arrow-down' }}" class="icon-xs"></i>
                                        @endif
                                    </a>
                                </th>
                                <th>Balance</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($categories as $category)
                                @php
                                    $balance = \App\Models\Transaction::forAccount($account->id)
                                        ->where('category_id', $category->id)
                                        ->get()
                                        ->sum(function($transaction) {
                                            return $transaction->type === 'income' ? $transaction->amount : -$transaction->amount;
                                        });
                                @endphp
                                <tr>
                                    <td><strong>{{ $category->name }}</strong></td>
                                    <td><span class="badge bg-{{ $category->type === 'income' ? 'success' : ($category->type === 'expense' ? 'danger' : 'info') }}">{{ ucfirst($category->type ?? 'N/A') }}</span></td>
                                    <td>
                                        <span class="fw-semibold {{ $balance >= 0 ? 'text-success' : 'text-danger' }}">
                                            {{ \App\Helpers\CurrencyHelper::format($balance, $currencyCode) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($category->is_archived)
                                            <span class="badge bg-secondary">Archived</span>
                                        @else
                                            <span class="badge bg-success">Active</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('categories.edit', $category) }}" class="btn btn-sm btn-primary">
                                            <i data-feather="edit-2" class="icon-xs"></i> Edit
                                        </a>
                                        <form action="{{ route('categories.destroy', $category) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this expense type?')">
                                                <i data-feather="trash-2" class="icon-xs"></i> Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <div class="text-muted">
                                            <i data-feather="tag" class="icon-lg mb-2"></i>
                                            <p class="mb-2">No expense types found.</p>
                                            <a href="{{ route('categories.create') }}" class="btn btn-primary">Add Your First Expense Type</a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($categories->hasPages())
                    <div class="d-flex justify-content-end mt-3">
                        {{ $categories->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
