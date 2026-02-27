@extends('layouts.app')

@section('title', 'Edit Recurring Transaction')

@php
    $currencyCode = $__currencyCode;
    $account = $__account;
@endphp

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('recurring.index') }}">Recurring</a></li>
    <li class="breadcrumb-item active">Edit Recurring</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Edit Recurring Transaction</h4>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('recurring.update', $recurring) }}" method="POST" class="needs-validation" novalidate>
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label">Type <span class="text-danger">*</span></label>
                        <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                            <option value="income" {{ old('type', $recurring->type) === 'income' ? 'selected' : '' }}>Income</option>
                            <option value="expense" {{ old('type', $recurring->type) === 'expense' ? 'selected' : '' }}>Expense</option>
                        </select>
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Amount ({{ $currencyCode }}) <span class="text-danger">*</span></label>
                        <input type="number" name="amount" class="form-control @error('amount') is-invalid @enderror" step="0.01" min="0.01" value="{{ old('amount', $recurring->amount) }}" required>
                        @error('amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Income Source <span class="text-danger">*</span></label>
                        <select name="wallet_id" class="form-select searchable-select @error('wallet_id') is-invalid @enderror" required>
                            <option value="">Select...</option>
                            @foreach($wallets as $wallet)
                                <option value="{{ $wallet->id }}" {{ old('wallet_id', $recurring->wallet_id) == $wallet->id ? 'selected' : '' }}>{{ $wallet->name }}</option>
                            @endforeach
                        </select>
                        @error('wallet_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Expense Type</label>
                        <select name="category_id" class="form-select searchable-select @error('category_id') is-invalid @enderror">
                            <option value="">None</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id', $recurring->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Frequency <span class="text-danger">*</span></label>
                        <select name="frequency" class="form-select @error('frequency') is-invalid @enderror" required>
                            <option value="daily" {{ old('frequency', $recurring->frequency) === 'daily' ? 'selected' : '' }}>Daily</option>
                            <option value="weekly" {{ old('frequency', $recurring->frequency) === 'weekly' ? 'selected' : '' }}>Weekly</option>
                            <option value="biweekly" {{ old('frequency', $recurring->frequency) === 'biweekly' ? 'selected' : '' }}>Every 2 Weeks</option>
                            <option value="monthly" {{ old('frequency', $recurring->frequency) === 'monthly' ? 'selected' : '' }}>Monthly</option>
                            <option value="yearly" {{ old('frequency', $recurring->frequency) === 'yearly' ? 'selected' : '' }}>Yearly</option>
                        </select>
                        @error('frequency')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">End Date <small class="text-muted">(optional)</small></label>
                        <input type="date" name="end_date" class="form-control @error('end_date') is-invalid @enderror" value="{{ old('end_date', $recurring->end_date?->toDateString()) }}">
                        @error('end_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Note</label>
                        <input type="text" name="note" class="form-control @error('note') is-invalid @enderror" value="{{ old('note', $recurring->note) }}">
                        @error('note')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active" {{ old('is_active', $recurring->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Active</label>
                        </div>
                    </div>
                    <div class="mb-3 p-3 bg-light rounded">
                        <small class="text-muted">
                            <strong>Schedule Info:</strong>
                            Started {{ $recurring->start_date->format('M d, Y') }} &middot;
                            Next due: {{ $recurring->next_due_date->format('M d, Y') }}
                            @if($recurring->last_generated_at)
                                &middot; Last generated: {{ $recurring->last_generated_at->format('M d, Y') }}
                            @endif
                        </small>
                    </div>
                    <div class="mb-3">
                        <button type="submit" class="btn btn-primary">
                            <i data-feather="save" class="align-middle me-1"></i> Update
                        </button>
                        <a href="{{ route('recurring.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
