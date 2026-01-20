@extends('layouts.app')

@section('title', 'Edit Transaction')

@php
    $accountContext = app(\App\Services\AccountContext::class);
    $account = $accountContext->account();
    $currencyCode = $account?->currency_code ?? 'IQD';
@endphp

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Edit Transaction</h4>
        </div>
        <p class="text-muted mb-4">Update transaction details. Note: Transactions older than 30 days cannot be edited.</p>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('transactions.update', $transaction) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label">Type <span class="text-danger">*</span></label>
                        <select name="type" id="transaction_type" class="form-select @error('type') is-invalid @enderror" required>
                            <option value="income" {{ old('type', $transaction->type) === 'income' ? 'selected' : '' }}>Income</option>
                            <option value="expense" {{ old('type', $transaction->type) === 'expense' ? 'selected' : '' }}>Expense</option>
                            <option value="transfer" {{ old('type', $transaction->type) === 'transfer' ? 'selected' : '' }}>Transfer</option>
                        </select>
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Income Source <span class="text-danger">*</span></label>
                        <select name="wallet_id" class="form-select @error('wallet_id') is-invalid @enderror" required>
                            @foreach($wallets as $wallet)
                                <option value="{{ $wallet->id }}" {{ old('wallet_id', $transaction->wallet_id) == $wallet->id ? 'selected' : '' }}>{{ $wallet->name }}</option>
                            @endforeach
                        </select>
                        @error('wallet_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3" id="category_field">
                        <label class="form-label">Expense Type</label>
                        <select name="category_id" class="form-select @error('category_id') is-invalid @enderror">
                            <option value="">None</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id', $transaction->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Amount ({{ $currencyCode }}) <span class="text-danger">*</span></label>
                        <input type="number" name="amount" class="form-control @error('amount') is-invalid @enderror" step="0.01" min="0.01" value="{{ old('amount', $transaction->amount) }}" required>
                        @error('amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Date & Time <span class="text-danger">*</span></label>
                        <input type="datetime-local" name="occurred_at" class="form-control @error('occurred_at') is-invalid @enderror" value="{{ old('occurred_at', $transaction->occurred_at->format('Y-m-d\TH:i')) }}" required>
                        @error('occurred_at')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Note</label>
                        <textarea name="note" class="form-control @error('note') is-invalid @enderror" rows="3">{{ old('note', $transaction->note) }}</textarea>
                        @error('note')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <button type="submit" class="btn btn-primary">
                            <i data-feather="save" class="align-middle me-1"></i> Update Transaction
                        </button>
                        <a href="{{ route('transactions.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
