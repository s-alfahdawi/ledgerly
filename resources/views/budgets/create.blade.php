@extends('layouts.app')

@section('title', 'New Budget')

@php
    $currencyCode = $__currencyCode;
    $account = $__account;
@endphp

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('budgets.index') }}">Budgets</a></li>
    <li class="breadcrumb-item active">Create Budget</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">New Budget</h4>
        </div>
        <p class="text-muted mb-4">Set a spending limit for an expense category.</p>
    </div>
</div>

<div class="row">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('budgets.store') }}" method="POST" class="needs-validation" novalidate>
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Expense Category <span class="text-danger">*</span></label>
                        <select name="category_id" class="form-select searchable-select @error('category_id') is-invalid @enderror" required>
                            <option value="">Select category...</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ old('category_id') == $category->id ? 'selected' : '' }}
                                    {{ in_array($category->id, $existingCategoryIds) ? 'disabled' : '' }}>
                                    {{ $category->name }}
                                    {{ in_array($category->id, $existingCategoryIds) ? '(budget exists)' : '' }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Budget Limit ({{ $currencyCode }}) <span class="text-danger">*</span></label>
                        <input type="number" name="amount" class="form-control @error('amount') is-invalid @enderror" step="0.01" min="0.01" value="{{ old('amount') }}" required>
                        @error('amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Period <span class="text-danger">*</span></label>
                        <select name="period" class="form-select @error('period') is-invalid @enderror" required>
                            <option value="monthly" {{ old('period', 'monthly') === 'monthly' ? 'selected' : '' }}>Monthly</option>
                            <option value="weekly" {{ old('period') === 'weekly' ? 'selected' : '' }}>Weekly</option>
                            <option value="yearly" {{ old('period') === 'yearly' ? 'selected' : '' }}>Yearly</option>
                        </select>
                        @error('period')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <button type="submit" class="btn btn-primary">
                            <i data-feather="save" class="align-middle me-1"></i> Create Budget
                        </button>
                        <a href="{{ route('budgets.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
