@extends('layouts.app')

@section('title', 'Recurring Transactions')

@php
    $currencyCode = $__currencyCode;
    $account = $__account;
@endphp

@section('breadcrumbs')
    <li class="breadcrumb-item active">Recurring Transactions</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Recurring Transactions</h4>
            <a href="{{ route('recurring.create') }}" class="btn btn-primary">
                <i data-feather="plus" class="align-middle me-1"></i> New Recurring
            </a>
        </div>
        <p class="text-muted mb-4">Automate repeating income and expenses. Transactions are generated daily based on the schedule you set.</p>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                @if($recurring->isEmpty())
                    <div class="text-center py-4">
                        <i data-feather="repeat" style="width:48px;height:48px;" class="text-muted mb-3"></i>
                        <h5 class="text-muted">No recurring transactions yet</h5>
                        <p class="text-muted">Set up automatic transactions for rent, salary, subscriptions, and more.</p>
                        <a href="{{ route('recurring.create') }}" class="btn btn-primary">
                            <i data-feather="plus" class="align-middle me-1"></i> Create Your First
                        </a>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-nowrap mb-0">
                            <thead>
                                <tr>
                                    <th>Status</th>
                                    <th>Type</th>
                                    <th>Amount</th>
                                    <th>Wallet</th>
                                    <th>Category</th>
                                    <th>Frequency</th>
                                    <th>Next Due</th>
                                    <th>Note</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recurring as $item)
                                    <tr class="{{ !$item->is_active ? 'text-muted' : '' }}">
                                        <td>
                                            @if($item->is_active)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-secondary">Paused</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge {{ $item->type === 'income' ? 'bg-success' : 'bg-danger' }}">
                                                {{ ucfirst($item->type) }}
                                            </span>
                                        </td>
                                        <td>
                                            <strong>{{ \App\Helpers\CurrencyHelper::format($item->amount, $currencyCode) }}</strong>
                                        </td>
                                        <td>{{ $item->wallet?->name ?? '-' }}</td>
                                        <td>{{ $item->category?->name ?? '-' }}</td>
                                        <td>{{ ucfirst($item->frequency) }}</td>
                                        <td>
                                            @if($item->is_active)
                                                {{ $item->next_due_date->format('M d, Y') }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>{{ \Illuminate\Support\Str::limit($item->note, 30) }}</td>
                                        <td class="text-end">
                                            <div class="d-flex gap-1 justify-content-end">
                                                <form method="POST" action="{{ route('recurring.toggle', $item) }}">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm {{ $item->is_active ? 'btn-outline-warning' : 'btn-outline-success' }}" title="{{ $item->is_active ? 'Pause' : 'Activate' }}">
                                                        <i data-feather="{{ $item->is_active ? 'pause' : 'play' }}" style="width:14px;height:14px;"></i>
                                                    </button>
                                                </form>
                                                <a href="{{ route('recurring.edit', $item) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                                    <i data-feather="edit-2" style="width:14px;height:14px;"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-outline-danger" title="Delete"
                                                    data-bs-toggle="modal" data-bs-target="#confirmModal"
                                                    data-action="{{ route('recurring.destroy', $item) }}"
                                                    data-message="Are you sure you want to delete this recurring transaction?">
                                                    <i data-feather="trash-2" style="width:14px;height:14px;"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<x-confirm-modal />
@endsection
