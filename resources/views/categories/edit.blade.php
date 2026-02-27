@extends('layouts.app')

@section('title', 'Edit Expense Type')

@php
    $account = $__account;
@endphp

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('categories.index') }}">Expense Types</a></li>
    <li class="breadcrumb-item active">Edit Expense Type</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Edit Expense Type</h4>
        </div>
        <p class="text-muted mb-4">Update your expense type details.</p>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('categories.update', $category) }}" method="POST" class="needs-validation" novalidate>
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label">Expense Type Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $category->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Type <span class="text-danger">*</span></label>
                        <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                            <option value="income" {{ old('type', $category->type) === 'income' ? 'selected' : '' }}>Income</option>
                            <option value="expense" {{ old('type', $category->type) === 'expense' ? 'selected' : '' }}>Expense</option>
                            <option value="both" {{ old('type', $category->type) === 'both' ? 'selected' : '' }}>Both</option>
                        </select>
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Color (optional)</label>
                        <input type="color" name="color" class="form-control form-control-color" value="{{ old('color', $category->color ?? '#667eea') }}">
                    </div>
                    <div class="mb-3">
                        <button type="submit" class="btn btn-primary">
                            <i data-feather="save" class="align-middle me-1"></i> Update Expense Type
                        </button>
                        <a href="{{ route('categories.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
