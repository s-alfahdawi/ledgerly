@extends('layouts.app')

@section('title', 'Settings')

@php
    $accountContext = app(\App\Services\AccountContext::class);
    $account = $accountContext->account();
@endphp

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Settings</h4>
        </div>
        <p class="text-muted mb-4">Manage your workspace settings including name and currency.</p>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Workspace Settings</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('settings.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label">Workspace Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $account->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Currency <span class="text-danger">*</span></label>
                        <select name="currency_code" class="form-select @error('currency_code') is-invalid @enderror" required>
                            <option value="IQD" {{ $account->currency_code === 'IQD' ? 'selected' : '' }}>Iraqi Dinar (IQD)</option>
                            <option value="USD" {{ $account->currency_code === 'USD' ? 'selected' : '' }}>US Dollar (USD)</option>
                        </select>
                        @error('currency_code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">You can change the currency from IQD to USD or vice versa.</small>
                    </div>
                    <div class="mb-3">
                        <button type="submit" class="btn btn-primary">
                            <i data-feather="save" class="align-middle me-1"></i> Update Settings
                        </button>
                        <a href="{{ route('dashboard') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
