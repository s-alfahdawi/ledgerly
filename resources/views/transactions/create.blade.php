@extends('layouts.app')

@section('title', 'Add Transaction')

@php
    $accountContext = app(\App\Services\AccountContext::class);
    $account = $accountContext->account();
    $currencyCode = $account?->currency_code ?? 'IQD';
@endphp

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Add Transaction</h4>
        </div>
        <p class="text-muted mb-4">Record a new income or expense transaction. Select the type, income source, expense type, and amount.</p>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('transactions.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Type <span class="text-danger">*</span></label>
                        <select name="type" id="transaction_type" class="form-select @error('type') is-invalid @enderror" required>
                            <option value="income" {{ old('type') === 'income' ? 'selected' : '' }}>Income</option>
                            <option value="expense" {{ old('type') === 'expense' ? 'selected' : '' }}>Expense</option>
                            <option value="transfer" {{ old('type') === 'transfer' ? 'selected' : '' }}>Transfer</option>
                        </select>
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3" id="wallet_field">
                        <label class="form-label">Income Source <span id="wallet_required" class="text-danger">*</span></label>
                        <select name="wallet_id" id="wallet_id" class="form-select @error('wallet_id') is-invalid @enderror">
                            <option value="">Select Income Source...</option>
                            @foreach($wallets as $wallet)
                                <option value="{{ $wallet->id }}" {{ old('wallet_id') == $wallet->id ? 'selected' : '' }}>{{ $wallet->name }}</option>
                            @endforeach
                        </select>
                        @error('wallet_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3" id="category_field">
                        <label class="form-label">Expense Type <span id="category_required" class="text-danger" style="display: none;">*</span></label>
                        <select name="category_id" id="category_id" class="form-select @error('category_id') is-invalid @enderror">
                            <option value="">None</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3" id="transfer_fields" style="display: none;">
                        <label class="form-label">To Income Source</label>
                        <select name="to_wallet_id" class="form-select @error('to_wallet_id') is-invalid @enderror">
                            <option value="">Select Income Source...</option>
                            @foreach($wallets as $wallet)
                                <option value="{{ $wallet->id }}" {{ old('to_wallet_id') == $wallet->id ? 'selected' : '' }}>{{ $wallet->name }}</option>
                            @endforeach
                        </select>
                        @error('to_wallet_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Amount ({{ $currencyCode }}) <span class="text-danger">*</span></label>
                        <input type="number" name="amount" class="form-control @error('amount') is-invalid @enderror" step="0.01" min="0.01" value="{{ old('amount') }}" required>
                        @error('amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Date & Time <span class="text-danger">*</span></label>
                        <input type="datetime-local" name="occurred_at" class="form-control @error('occurred_at') is-invalid @enderror" value="{{ old('occurred_at', now()->format('Y-m-d\TH:i')) }}" required>
                        @error('occurred_at')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Note</label>
                        <textarea name="note" class="form-control @error('note') is-invalid @enderror" rows="3">{{ old('note') }}</textarea>
                        @error('note')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <button type="submit" class="btn btn-primary">
                            <i data-feather="save" class="align-middle me-1"></i> Create Transaction
                        </button>
                        <a href="{{ route('transactions.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    const typeSelect = document.getElementById('transaction_type');
    const walletInput = document.getElementById('wallet_id');
    const categoryInput = document.getElementById('category_id');
    const walletRequired = document.getElementById('wallet_required');
    const categoryRequired = document.getElementById('category_required');
    const categoryField = document.getElementById('category_field');
    const transferFields = document.getElementById('transfer_fields');

    function updateRequired() {
        const type = typeSelect.value;
        if (type === 'transfer') {
            categoryField.style.display = 'none';
            transferFields.style.display = 'block';
            walletRequired.style.display = 'inline';
            categoryRequired.style.display = 'none';
            walletInput.required = true;
            categoryInput.required = false;
        } else if (type === 'income') {
            categoryField.style.display = 'block';
            transferFields.style.display = 'none';
            walletRequired.style.display = 'inline';
            categoryRequired.style.display = 'none';
            walletInput.required = true;
            categoryInput.required = false;
        } else {
            categoryField.style.display = 'block';
            transferFields.style.display = 'none';
            walletRequired.style.display = 'none';
            categoryRequired.style.display = 'inline';
            walletInput.required = false;
            categoryInput.required = true;
        }
    }

    typeSelect.addEventListener('change', updateRequired);
    updateRequired();
})();
</script>
@endsection
