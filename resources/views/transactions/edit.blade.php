@extends('layouts.app')

@section('title', 'Edit Transaction')

@php
    $currencyCode = $__currencyCode;
    $account = $__account;
@endphp

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('transactions.index') }}">Transactions</a></li>
    <li class="breadcrumb-item active">Edit Transaction</li>
@endsection

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
                <form action="{{ route('transactions.update', $transaction) }}" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
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
                    <div class="mb-3" id="wallet_field">
                        <label class="form-label">Income Source <span id="wallet_required" class="text-danger">*</span></label>
                        <select name="wallet_id" id="wallet_id" class="form-select searchable-select @error('wallet_id') is-invalid @enderror">
                            <option value="" {{ old('wallet_id', $transaction->wallet_id) === '' || old('wallet_id', $transaction->wallet_id) === null ? 'selected' : '' }}>Select Income Source...</option>
                            @foreach($wallets as $wallet)
                                <option value="{{ $wallet->id }}" {{ old('wallet_id', $transaction->wallet_id) == $wallet->id ? 'selected' : '' }}>{{ $wallet->name }}</option>
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
                    @if(isset($tags) && $tags->isNotEmpty())
                    @php $selectedTags = old('tags', $transaction->tags->pluck('id')->toArray()); @endphp
                    <div class="mb-3">
                        <label class="form-label">Tags</label>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($tags as $tag)
                                <div>
                                    <input type="checkbox" name="tags[]" value="{{ $tag->id }}" class="btn-check" id="tag_edit_{{ $tag->id }}" autocomplete="off" {{ in_array($tag->id, $selectedTags) ? 'checked' : '' }}>
                                    <label class="btn btn-sm btn-outline-secondary" for="tag_edit_{{ $tag->id }}">
                                        <span class="d-inline-block rounded-circle me-1" style="width:8px;height:8px;background:{{ $tag->color }}"></span>{{ $tag->name }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        <small class="text-muted mt-1 d-block"><a href="{{ route('tags.index') }}">Manage tags</a></small>
                    </div>
                    @endif
                    @if($transaction->attachments->isNotEmpty())
                    <div class="mb-3">
                        <label class="form-label">Current Attachments</label>
                        <div class="list-group">
                            @foreach($transaction->attachments as $attachment)
                                <div class="list-group-item d-flex align-items-center justify-content-between">
                                    <div>
                                        @if($attachment->isImage())
                                            <i data-feather="image" class="icon-xs me-2 text-info"></i>
                                        @else
                                            <i data-feather="file" class="icon-xs me-2 text-secondary"></i>
                                        @endif
                                        <a href="{{ route('attachments.show', $attachment) }}">{{ $attachment->filename }}</a>
                                        <small class="text-muted ms-2">{{ $attachment->human_size }}</small>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-danger"
                                        data-bs-toggle="modal" data-bs-target="#confirmModal"
                                        data-action="{{ route('attachments.destroy', $attachment) }}"
                                        data-message="Are you sure you want to delete this attachment?">
                                        <i data-feather="trash-2" class="icon-xs"></i>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                    <div class="mb-3">
                        <label class="form-label">Add Receipts / Attachments</label>
                        <input type="file" name="attachments[]" class="form-control @error('attachments.*') is-invalid @enderror" multiple accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.csv,.txt">
                        <small class="text-muted">Upload receipts, invoices, or documents. Max 5 files, 10MB each.</small>
                        @error('attachments.*')
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

<script>
(function() {
    const typeSelect = document.getElementById('transaction_type');
    const walletInput = document.getElementById('wallet_id');
    const categoryInput = document.getElementById('category_id');
    const walletRequired = document.getElementById('wallet_required');
    const categoryRequired = document.getElementById('category_required');
    const categoryField = document.getElementById('category_field');

    function updateRequired() {
        const type = typeSelect.value;
        if (type === 'transfer' || type === 'income') {
            walletRequired.style.display = 'inline';
            categoryRequired.style.display = 'none';
            walletInput.required = true;
            categoryInput.required = false;
        } else {
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

<x-confirm-modal />
@endsection
