@extends('layouts.app')

@section('title', 'Import Preview')

@php
    $currencyCode = $__currencyCode;
    $account = $__account;
@endphp

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('import.index') }}">Import</a></li>
    <li class="breadcrumb-item active">Preview</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Import Preview</h4>
        </div>
        <p class="text-muted mb-4">Map your CSV columns to transaction fields, then import.</p>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">CSV Data Preview ({{ count($rows) }} rows)</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive mb-4" style="max-height: 300px; overflow-y: auto;">
                    <table class="table table-sm table-bordered mb-0">
                        <thead class="table-light">
                            <tr>
                                @foreach($headers as $i => $header)
                                    <th><small class="text-muted">Col {{ $i }}:</small> {{ $header }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach(array_slice($rows, 0, 5) as $row)
                                <tr>
                                    @foreach($row as $cell)
                                        <td class="small">{{ \Illuminate\Support\Str::limit($cell, 40) }}</td>
                                    @endforeach
                                </tr>
                            @endforeach
                            @if(count($rows) > 5)
                                <tr>
                                    <td colspan="{{ count($headers) }}" class="text-center text-muted small">
                                        ... and {{ count($rows) - 5 }} more rows
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Column Mapping</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('import.process') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Date Column <span class="text-danger">*</span></label>
                            <select name="date_column" class="form-select" required>
                                @foreach($headers as $i => $header)
                                    <option value="{{ $i }}" {{ stripos($header, 'date') !== false ? 'selected' : '' }}>{{ $header }} (Col {{ $i }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Amount Column <span class="text-danger">*</span></label>
                            <select name="amount_column" class="form-select" required>
                                @foreach($headers as $i => $header)
                                    <option value="{{ $i }}" {{ stripos($header, 'amount') !== false ? 'selected' : '' }}>{{ $header }} (Col {{ $i }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Description/Note Column</label>
                            <select name="note_column" class="form-select">
                                <option value="">-- None --</option>
                                @foreach($headers as $i => $header)
                                    <option value="{{ $i }}" {{ (stripos($header, 'note') !== false || stripos($header, 'desc') !== false || stripos($header, 'memo') !== false) ? 'selected' : '' }}>{{ $header }} (Col {{ $i }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Type Column</label>
                            <select name="type_column" class="form-select">
                                <option value="">-- None (use default) --</option>
                                @foreach($headers as $i => $header)
                                    <option value="{{ $i }}" {{ stripos($header, 'type') !== false ? 'selected' : '' }}>{{ $header }} (Col {{ $i }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <hr>
                    <h6 class="text-muted mb-3">Import Settings</h6>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Import to Wallet <span class="text-danger">*</span></label>
                            <select name="wallet_id" class="form-select" required>
                                @foreach($wallets as $wallet)
                                    <option value="{{ $wallet->id }}">{{ $wallet->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Default Category</label>
                            <select name="default_category_id" class="form-select">
                                <option value="">None</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Transaction Type <span class="text-danger">*</span></label>
                            <select name="default_type" class="form-select" required>
                                <option value="auto">Auto-detect (negative=expense)</option>
                                <option value="expense">All Expense</option>
                                <option value="income">All Income</option>
                            </select>
                        </div>
                    </div>

                    <div class="mt-3">
                        <button type="submit" class="btn btn-success">
                            <i data-feather="check" class="align-middle me-1"></i> Import {{ count($rows) }} Transactions
                        </button>
                        <a href="{{ route('import.index') }}" class="btn btn-secondary">Back</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
