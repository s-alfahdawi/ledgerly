@extends('layouts.app')

@section('title', 'Budgets')

@php
    $currencyCode = $__currencyCode;
    $account = $__account;
@endphp

@section('breadcrumbs')
    <li class="breadcrumb-item active">Budgets</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Budgets</h4>
            <a href="{{ route('budgets.create') }}" class="btn btn-primary">
                <i data-feather="plus" class="align-middle me-1"></i> New Budget
            </a>
        </div>
        <p class="text-muted mb-4">Set spending limits per category and track your progress this month.</p>
    </div>
</div>

@if(!empty($budgetProgress))
<div class="row">
    @foreach($budgetProgress as $bp)
        <div class="col-xl-4 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <h6 class="mb-0">{{ $bp['category'] }}</h6>
                            <small class="text-muted">{{ ucfirst($bp['period']) }} budget</small>
                        </div>
                        @if($bp['over_budget'])
                            <span class="badge bg-danger">Over Budget</span>
                        @elseif($bp['percentage'] >= 80)
                            <span class="badge bg-warning">{{ $bp['percentage'] }}%</span>
                        @else
                            <span class="badge bg-success">{{ $bp['percentage'] }}%</span>
                        @endif
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted small">{{ \App\Helpers\CurrencyHelper::format($bp['spent'], $currencyCode) }} spent</span>
                        <span class="text-muted small">{{ \App\Helpers\CurrencyHelper::format($bp['limit'], $currencyCode) }} limit</span>
                    </div>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar {{ $bp['over_budget'] ? 'bg-danger' : ($bp['percentage'] >= 80 ? 'bg-warning' : 'bg-success') }}"
                             role="progressbar"
                             style="width: {{ min($bp['percentage'], 100) }}%"
                             aria-valuenow="{{ $bp['percentage'] }}"
                             aria-valuemin="0"
                             aria-valuemax="100">
                        </div>
                    </div>
                    @if($bp['over_budget'])
                        <small class="text-danger mt-1 d-block">
                            Over by {{ \App\Helpers\CurrencyHelper::format($bp['spent'] - $bp['limit'], $currencyCode) }}
                        </small>
                    @else
                        <small class="text-muted mt-1 d-block">
                            {{ \App\Helpers\CurrencyHelper::format($bp['remaining'], $currencyCode) }} remaining
                        </small>
                    @endif
                </div>
            </div>
        </div>
    @endforeach
</div>
@endif

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">All Budgets</h5>
            </div>
            <div class="card-body">
                @if($budgets->isEmpty())
                    <div class="text-center py-4">
                        <i data-feather="target" style="width:48px;height:48px;" class="text-muted mb-3"></i>
                        <h5 class="text-muted">No budgets set</h5>
                        <p class="text-muted">Create budgets to track spending limits per category.</p>
                        <a href="{{ route('budgets.create') }}" class="btn btn-primary">
                            <i data-feather="plus" class="align-middle me-1"></i> Create Your First Budget
                        </a>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-nowrap mb-0">
                            <thead>
                                <tr>
                                    <th>Category</th>
                                    <th>Limit</th>
                                    <th>Period</th>
                                    <th>Status</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($budgets as $budget)
                                    <tr>
                                        <td>
                                            @if($budget->category)
                                                <span class="d-inline-block rounded-circle me-1" style="width:10px;height:10px;background:{{ $budget->category->color }}"></span>
                                                {{ $budget->category->name }}
                                            @else
                                                Overall
                                            @endif
                                        </td>
                                        <td><strong>{{ \App\Helpers\CurrencyHelper::format($budget->amount, $currencyCode) }}</strong></td>
                                        <td>{{ ucfirst($budget->period) }}</td>
                                        <td>
                                            @if($budget->is_active)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-secondary">Inactive</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ route('budgets.edit', $budget) }}" class="btn btn-sm btn-outline-primary">
                                                <i data-feather="edit-2" style="width:14px;height:14px;"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-danger"
                                                data-bs-toggle="modal" data-bs-target="#confirmModal"
                                                data-action="{{ route('budgets.destroy', $budget) }}"
                                                data-message="Are you sure you want to delete this budget?">
                                                <i data-feather="trash-2" style="width:14px;height:14px;"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<x-confirm-modal />
@endsection
