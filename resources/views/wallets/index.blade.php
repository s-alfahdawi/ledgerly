@extends('layouts.app')

@section('title', 'Income Sources')

@php
    $currencyCode = $__currencyCode;
    $account = $__account;
@endphp

@section('breadcrumbs')
    <li class="breadcrumb-item active">Income Sources</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Income Sources</h4>
            <div class="page-title-right">
                <a href="{{ route('wallets.create') }}" class="btn btn-primary">
                    <i data-feather="plus" class="align-middle me-1"></i> Add Income Source
                </a>
            </div>
        </div>
        <p class="text-muted mb-4">Manage your income sources like freelance work, payroll, investments, etc. Each income source tracks where your money comes from.</p>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h4 class="card-title mb-0">All Income Sources</h4>
                    </div>
                    <div class="col-md-6">
                        <form method="GET" action="{{ route('wallets.index') }}" class="d-flex gap-2">
                            <input type="text" name="search" class="form-control" placeholder="Search by name..." value="{{ request('search') }}">
                            <button type="submit" class="btn btn-primary"><i data-feather="search"></i></button>
                            @if(request('search'))
                                <a href="{{ route('wallets.index') }}" class="btn btn-secondary"><i data-feather="x"></i></a>
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
                                    <a href="{{ route('wallets.index', array_merge(request()->all(), ['sort' => 'name', 'direction' => request('sort') == 'name' && request('direction') == 'asc' ? 'desc' : 'asc'])) }}" class="text-dark text-decoration-none">
                                        Name
                                        @if(request('sort') == 'name')
                                            <i data-feather="{{ request('direction') == 'asc' ? 'arrow-up' : 'arrow-down' }}" class="icon-xs"></i>
                                        @endif
                                    </a>
                                </th>
                                <th>
                                    <a href="{{ route('wallets.index', array_merge(request()->all(), ['sort' => 'type', 'direction' => request('sort') == 'type' && request('direction') == 'asc' ? 'desc' : 'asc'])) }}" class="text-dark text-decoration-none">
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
                            @forelse($wallets as $wallet)
                                <tr>
                                    <td><strong>{{ $wallet->name }}</strong></td>
                                    <td><span class="badge bg-info">{{ ucfirst($wallet->type ?? 'N/A') }}</span></td>
                                    <td>
                                        @php
                                            $balance = $wallet->opening_balance + $wallet->transactions->where('type', 'income')->sum('amount') - $wallet->transactions->where('type', 'expense')->sum('amount');
                                        @endphp
                                        <span class="fw-semibold {{ $balance >= 0 ? 'text-success' : 'text-danger' }}">
                                            {{ \App\Helpers\CurrencyHelper::format($balance, $currencyCode) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($wallet->is_archived)
                                            <span class="badge bg-secondary">Archived</span>
                                        @else
                                            <span class="badge bg-success">Active</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('wallets.edit', $wallet) }}" class="btn btn-sm btn-primary">
                                            <i data-feather="edit-2" class="icon-xs"></i> Edit
                                        </a>
                                        <button type="button" class="btn btn-sm btn-danger"
                                            data-bs-toggle="modal" data-bs-target="#confirmModal"
                                            data-action="{{ route('wallets.destroy', $wallet) }}"
                                            data-message="Are you sure you want to delete this income source?">
                                            <i data-feather="trash-2" class="icon-xs"></i> Delete
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <div class="text-muted">
                                            <i data-feather="trending-up" class="icon-lg mb-2"></i>
                                            <p class="mb-2">No income sources found.</p>
                                            <a href="{{ route('wallets.create') }}" class="btn btn-primary">Add Your First Income Source</a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($wallets->hasPages())
                    <div class="d-flex justify-content-end mt-3">
                        {{ $wallets->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<x-confirm-modal />
@endsection
