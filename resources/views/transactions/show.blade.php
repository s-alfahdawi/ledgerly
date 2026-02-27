@extends('layouts.app')

@section('title', 'Transaction Details')

@php
    $currencyCode = $__currencyCode;
    $account = $__account;
@endphp

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('transactions.index') }}">Transactions</a></li>
    <li class="breadcrumb-item active">Transaction Details</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Transaction Details</h4>
            <div class="page-title-right">
                <a href="{{ route('transactions.index') }}" class="btn btn-secondary">
                    <i data-feather="arrow-left" class="align-middle me-1"></i> Back to Transactions
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center mb-4">
                    <div class="flex-grow-1">
                        <h5 class="card-title mb-1">
                            <span class="badge bg-{{ $transaction->type === 'income' ? 'success' : ($transaction->type === 'expense' ? 'danger' : 'info') }} fs-6">
                                {{ ucfirst($transaction->type) }}
                            </span>
                        </h5>
                        <p class="text-muted mb-0">
                            {{ $transaction->occurred_at->format('l, F j, Y \a\t g:i A') }}
                        </p>
                    </div>
                    <div class="text-end">
                        <h3 class="{{ $transaction->type === 'income' ? 'text-success' : ($transaction->type === 'expense' ? 'text-danger' : 'text-info') }} mb-0">
                            {{ \App\Helpers\CurrencyHelper::format($transaction->amount, $currencyCode) }}
                        </h3>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-borderless mb-0">
                        <tbody>
                            @if($transaction->wallet)
                            <tr>
                                <th style="width: 180px;" class="text-muted">Income Source</th>
                                <td>{{ $transaction->wallet->name }}</td>
                            </tr>
                            @endif
                            @if($transaction->category)
                            <tr>
                                <th class="text-muted">Expense Type</th>
                                <td>{{ $transaction->category->name }}</td>
                            </tr>
                            @endif
                            @if($transaction->note)
                            <tr>
                                <th class="text-muted">Note</th>
                                <td>{{ $transaction->note }}</td>
                            </tr>
                            @endif
                            @if($transaction->tags && $transaction->tags->isNotEmpty())
                            <tr>
                                <th class="text-muted">Tags</th>
                                <td>
                                    @foreach($transaction->tags as $tag)
                                        <span class="badge" style="background-color: {{ $tag->color }}">{{ $tag->name }}</span>
                                    @endforeach
                                </td>
                            </tr>
                            @endif
                            @if($transaction->transfer_ref)
                            <tr>
                                <th class="text-muted">Transfer Reference</th>
                                <td><code>{{ $transaction->transfer_ref }}</code></td>
                            </tr>
                            @endif
                            <tr>
                                <th class="text-muted">Created</th>
                                <td>
                                    {{ $transaction->created_at->format('M d, Y g:i A') }}
                                    @if($transaction->creator)
                                        by {{ $transaction->creator->name }}
                                    @endif
                                </td>
                            </tr>
                            @if($transaction->updater)
                            <tr>
                                <th class="text-muted">Last Updated</th>
                                <td>
                                    {{ $transaction->updated_at->format('M d, Y g:i A') }}
                                    by {{ $transaction->updater->name }}
                                </td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    <a href="{{ route('transactions.edit', $transaction) }}" class="btn btn-primary">
                        <i data-feather="edit-2" class="align-middle me-1"></i> Edit
                    </a>
                    <button type="button" class="btn btn-danger"
                        data-bs-toggle="modal" data-bs-target="#confirmModal"
                        data-action="{{ route('transactions.destroy', $transaction) }}"
                        data-message="Are you sure you want to delete this transaction?">
                        <i data-feather="trash-2" class="align-middle me-1"></i> Delete
                    </button>
                </div>
            </div>
        </div>

        @if($transaction->attachments && $transaction->attachments->isNotEmpty())
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i data-feather="paperclip" class="align-middle me-1"></i> Attachments ({{ $transaction->attachments->count() }})
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    @foreach($transaction->attachments as $attachment)
                        <div class="col-md-6">
                            <div class="border rounded p-3 d-flex align-items-center">
                                <div class="flex-shrink-0 me-3">
                                    @if($attachment->isImage())
                                        <i data-feather="image" class="text-info" style="width:32px;height:32px;"></i>
                                    @else
                                        <i data-feather="file" class="text-secondary" style="width:32px;height:32px;"></i>
                                    @endif
                                </div>
                                <div class="flex-grow-1 overflow-hidden">
                                    <h6 class="mb-1 text-truncate">
                                        <a href="{{ route('attachments.show', $attachment) }}">{{ $attachment->filename }}</a>
                                    </h6>
                                    <small class="text-muted">{{ $attachment->human_size }} &middot; {{ strtoupper(pathinfo($attachment->filename, PATHINFO_EXTENSION)) }}</small>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<x-confirm-modal />
@endsection
