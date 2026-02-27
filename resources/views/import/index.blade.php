@extends('layouts.app')

@section('title', 'Import Transactions')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Import</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Import Transactions</h4>
        </div>
        <p class="text-muted mb-4">Upload a CSV file from your bank or spreadsheet to import transactions in bulk.</p>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title mb-3">Upload CSV File</h5>

                <div class="alert alert-info">
                    <strong>CSV Requirements:</strong>
                    <ul class="mb-0 mt-1">
                        <li>First row must contain column headers</li>
                        <li>Must include at least a date column and an amount column</li>
                        <li>Supported date formats: YYYY-MM-DD, MM/DD/YYYY, DD/MM/YYYY</li>
                        <li>Maximum file size: 2MB (up to 100 rows)</li>
                    </ul>
                </div>

                <form action="{{ route('import.preview') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">CSV File <span class="text-danger">*</span></label>
                        <input type="file" name="csv_file" class="form-control @error('csv_file') is-invalid @enderror" accept=".csv,.txt" required>
                        @error('csv_file')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i data-feather="upload" class="align-middle me-1"></i> Upload & Preview
                    </button>
                    <a href="{{ route('transactions.index') }}" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
