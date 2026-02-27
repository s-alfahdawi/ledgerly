@extends('layouts.app')

@section('title', 'Add Transaction')

@php
    $currencyCode = $__currencyCode;
    $account = $__account;
    $d = $duplicate ?? null;
@endphp

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('transactions.index') }}">Transactions</a></li>
    <li class="breadcrumb-item active">Add Transaction</li>
@endsection

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
                <form action="{{ route('transactions.store') }}" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Type <span class="text-danger">*</span></label>
                        <select name="type" id="transaction_type" class="form-select @error('type') is-invalid @enderror" required>
                            <option value="income" {{ old('type', $d?->type) === 'income' ? 'selected' : '' }}>Income</option>
                            <option value="expense" {{ old('type', $d?->type) === 'expense' ? 'selected' : '' }}>Expense</option>
                            <option value="transfer" {{ old('type', $d?->type) === 'transfer' ? 'selected' : '' }}>Transfer</option>
                        </select>
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3" id="wallet_field">
                        <label class="form-label">Income Source <span id="wallet_required" class="text-danger">*</span></label>
                        <select name="wallet_id" id="wallet_id" class="form-select searchable-select @error('wallet_id') is-invalid @enderror">
                            <option value="">Select Income Source...</option>
                            @foreach($wallets as $wallet)
                                <option value="{{ $wallet->id }}" {{ old('wallet_id', $d?->wallet_id) == $wallet->id ? 'selected' : '' }}>{{ $wallet->name }}</option>
                            @endforeach
                        </select>
                        @error('wallet_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3" id="category_field">
                        <label class="form-label">Expense Type <span id="category_required" class="text-danger" style="display: none;">*</span></label>
                        <select name="category_id" id="category_id" class="form-select searchable-select @error('category_id') is-invalid @enderror">
                            <option value="">None</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id', $d?->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3" id="transfer_fields" style="display: none;">
                        <label class="form-label">To Income Source</label>
                        <select name="to_wallet_id" class="form-select searchable-select @error('to_wallet_id') is-invalid @enderror">
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
                        <input type="number" name="amount" class="form-control @error('amount') is-invalid @enderror" step="0.01" min="0.01" value="{{ old('amount', $d?->amount) }}" required>
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
                        <textarea name="note" class="form-control @error('note') is-invalid @enderror" rows="3">{{ old('note', $d?->note) }}</textarea>
                        @error('note')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    @if(isset($tags) && $tags->isNotEmpty())
                    <div class="mb-3">
                        <label class="form-label">Tags</label>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($tags as $tag)
                                <div>
                                    <input type="checkbox" name="tags[]" value="{{ $tag->id }}" class="btn-check" id="tag_create_{{ $tag->id }}" autocomplete="off" {{ in_array($tag->id, old('tags', $d ? $d->tags->pluck('id')->all() : [])) ? 'checked' : '' }}>
                                    <label class="btn btn-sm btn-outline-secondary" for="tag_create_{{ $tag->id }}">
                                        <span class="d-inline-block rounded-circle me-1" style="width:8px;height:8px;background:{{ $tag->color }}"></span>{{ $tag->name }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        <small class="text-muted mt-1 d-block"><a href="{{ route('tags.index') }}">Manage tags</a></small>
                    </div>
                    @endif
                    <div class="mb-3">
                        <label class="form-label">Receipts / Attachments</label>
                        <input type="file" name="attachments[]" class="form-control @error('attachments.*') is-invalid @enderror" multiple accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.csv,.txt">
                        <small class="text-muted">Upload receipts, invoices, or documents. Max 5 files, 10MB each. Supports images, PDF, and Office files.</small>
                        @error('attachments.*')
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
