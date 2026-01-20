@extends('layouts.app')

@section('title', 'Add Expense Type')

@php
    $accountContext = app(\App\Services\AccountContext::class);
    $account = $accountContext->account();
@endphp

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Add Expense Type</h4>
        </div>
        <p class="text-muted mb-4">Create a new expense type to categorize your spending (e.g., rent, food, gas, utilities).</p>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('categories.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Expense Type Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="e.g., Rent, Food, Gas, Utilities" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Enter the name of your expense type</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Type <span class="text-danger">*</span></label>
                        <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                            <option value="expense" selected>Expense</option>
                            <option value="income">Income</option>
                            <option value="both">Both</option>
                        </select>
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Select if this is for expenses, income, or both</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Color (optional)</label>
                        <input type="color" name="color" class="form-control form-control-color" value="#667eea">
                        <small class="text-muted">Choose a color to identify this expense type</small>
                    </div>
                    <div class="mb-3">
                        <button type="submit" class="btn btn-primary">
                            <i data-feather="save" class="align-middle me-1"></i> Create Expense Type
                        </button>
                        <a href="{{ route('categories.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
