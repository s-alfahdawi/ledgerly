@extends('layouts.app')

@section('title', 'Add Income Source')

@php
    $currencyCode = $__currencyCode;
    $account = $__account;
@endphp

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('wallets.index') }}">Income Sources</a></li>
    <li class="breadcrumb-item active">Add Income Source</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Add Income Source</h4>
        </div>
        <p class="text-muted mb-4">Create a new income source to track where your money comes from (e.g., freelance, payroll, investments).</p>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('wallets.store') }}" method="POST" class="needs-validation" novalidate>
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Income Source Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="e.g., Freelance, Payroll, Investment" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Enter the name of your income source</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Type <span class="text-danger">*</span></label>
                        <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                            <option value="">Select type...</option>
                            <option value="cash" {{ old('type') === 'cash' ? 'selected' : '' }}>Cash</option>
                            <option value="bank" {{ old('type') === 'bank' ? 'selected' : '' }}>Bank</option>
                            <option value="card" {{ old('type') === 'card' ? 'selected' : '' }}>Card</option>
                            <option value="other" {{ old('type') === 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Opening Balance ({{ $currencyCode }})</label>
                        <input type="number" name="opening_balance" class="form-control @error('opening_balance') is-invalid @enderror" step="0.01" value="0" required>
                        @error('opening_balance')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Starting balance for this income source</small>
                    </div>
                    <div class="mb-3">
                        <button type="submit" class="btn btn-primary">
                            <i data-feather="save" class="align-middle me-1"></i> Create Income Source
                        </button>
                        <a href="{{ route('wallets.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
