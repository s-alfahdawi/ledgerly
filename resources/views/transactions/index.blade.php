@extends('layouts.app')

@section('title', 'Transactions')

@php
    $currencyCode = $__currencyCode;
    $account = $__account;
@endphp

@section('breadcrumbs')
    <li class="breadcrumb-item active">Transactions</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Transactions</h4>
            <div class="page-title-right d-flex gap-2">
                <a href="{{ route('import.index') }}" class="btn btn-outline-primary">
                    <i data-feather="upload" class="align-middle me-1"></i> Import CSV
                </a>
                <a href="{{ route('transactions.create') }}" class="btn btn-primary">
                    <i data-feather="plus" class="align-middle me-1"></i> Add Transaction
                </a>
            </div>
        </div>
        <p class="text-muted mb-4">View and manage all your income and expense transactions. Filter by type, date range, income source, or expense type.</p>
    </div>
</div>

{{-- ============================================================== --}}
{{-- CASH MATCH (Reconcile actual cash on hand with system balance) --}}
{{-- ============================================================== --}}
<div class="row mb-3">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0"><i data-feather="check-circle" class="icon-xs me-1"></i> Match Cash on Hand</h4>
                <span class="badge bg-light text-muted">Creates an adjustment transaction</span>
            </div>
            <div class="card-body">
                @if(session('info'))
                    <div class="alert alert-info py-2 mb-3">{{ session('info') }}</div>
                @endif
                @php $currentCash = (float) ($currentCashTotal ?? 0); @endphp
                <form method="POST" action="{{ route('transactions.cash-match') }}"
                      x-data="{
                          actual: '',
                          current: {{ $currentCash }},
                          get diff() {
                              var actual = parseFloat(this.actual);
                              if (isNaN(actual)) return null;
                              return Math.round((actual - this.current) * 100) / 100;
                          }
                      }">
                    @csrf
                    <div class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label">Current Cash (system)</label>
                            <input type="text" class="form-control" readonly
                                   value="{{ \App\Helpers\CurrencyHelper::format($currentCash, $currencyCode) }}">
                            <small class="text-muted">Sum of all wallets: opening + income − expense</small>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Actual Cash on Hand</label>
                            <input type="number" name="actual_cash" class="form-control" step="0.01" min="0" x-model="actual" placeholder="0.00" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Note (optional)</label>
                            <input type="text" name="note" class="form-control" maxlength="500" placeholder="Reason for adjustment">
                        </div>
                        <div class="col-md-2 d-grid">
                            <button type="submit" class="btn btn-primary" :disabled="diff === null || Math.abs(diff) < 0.01">
                                <i data-feather="check" class="icon-xs me-1"></i> Settle
                            </button>
                        </div>
                    </div>
                    <div class="mt-3 font-size-13" x-show="diff !== null" x-cloak>
                        <template x-if="Math.abs(diff) < 0.01">
                            <span class="text-success"><i class="mdi mdi-check-circle"></i> Already balanced — no transaction will be created.</span>
                        </template>
                        <template x-if="diff > 0.005">
                            <span>
                                Will create an
                                <span class="badge bg-success-subtle text-success">income</span>
                                adjustment of
                                <strong x-text="diff.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})"></strong>
                                {{ $currencyCode }} (you have more cash than the system shows).
                            </span>
                        </template>
                        <template x-if="diff < -0.005">
                            <span>
                                Will create an
                                <span class="badge bg-danger-subtle text-danger">expense</span>
                                adjustment of
                                <strong x-text="Math.abs(diff).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})"></strong>
                                {{ $currencyCode }} (you have less cash than the system shows).
                            </span>
                        </template>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row mb-3">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ route('transactions.index') }}" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Type</label>
                        <select name="type" class="form-select">
                            <option value="">All Types</option>
                            <option value="income" {{ request('type') === 'income' ? 'selected' : '' }}>Income</option>
                            <option value="expense" {{ request('type') === 'expense' ? 'selected' : '' }}>Expense</option>
                            <option value="transfer" {{ request('type') === 'transfer' ? 'selected' : '' }}>Transfer</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Income Source</label>
                        <select name="wallet_id" class="form-select">
                            <option value="">All Sources</option>
                            @foreach(\App\Models\Wallet::forAccount($account->id)->active()->get() as $wallet)
                                <option value="{{ $wallet->id }}" {{ request('wallet_id') == $wallet->id ? 'selected' : '' }}>{{ $wallet->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Expense Type</label>
                        <select name="category_id" class="form-select">
                            <option value="">All Types</option>
                            @foreach(\App\Models\Category::forAccount($account->id)->active()->get() as $category)
                                <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Search</label>
                        <input type="text" name="search" class="form-control" placeholder="Search notes..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Start Date</label>
                        <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">End Date</label>
                        <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Tag</label>
                        <select name="tag_id" class="form-select">
                            <option value="">All Tags</option>
                            @foreach(\App\Models\Tag::forAccount($account->id)->orderBy('name')->get() as $tag)
                                <option value="{{ $tag->id }}" {{ request('tag_id') == $tag->id ? 'selected' : '' }}>{{ $tag->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i data-feather="filter" class="align-middle me-1"></i> Apply Filters
                        </button>
                        <a href="{{ route('transactions.index') }}" class="btn btn-secondary">
                            <i data-feather="x" class="align-middle me-1"></i> Clear
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Bulk Action Toolbar (hidden until checkboxes selected) -->
<div class="row mb-3" id="bulk-toolbar" style="display: none;">
    <div class="col-12">
        <div class="card border-primary">
            <div class="card-body py-2">
                <form id="bulk-form" method="POST" action="{{ route('transactions.bulk') }}" class="d-flex align-items-center gap-3 flex-wrap">
                    @csrf
                    <div id="bulk-ids-container"></div>
                    <span class="fw-semibold"><span id="bulk-count">0</span> selected</span>
                    <div class="vr d-none d-sm-block"></div>
                    <div class="d-flex align-items-center gap-2">
                        <select id="bulk-action-select" name="action" class="form-select form-select-sm" style="width: auto;" required>
                            <option value="">Choose action...</option>
                            <option value="delete">Delete Selected</option>
                            <option value="set_category">Set Category</option>
                            <option value="add_tag">Add Tag</option>
                            <option value="remove_tag">Remove Tag</option>
                        </select>
                        <select id="bulk-category" name="category_id" class="form-select form-select-sm" style="width: auto; display: none;">
                            @foreach(\App\Models\Category::forAccount($account->id)->active()->get() as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                        <select id="bulk-tag" name="tag_id" class="form-select form-select-sm" style="width: auto; display: none;">
                            @foreach(\App\Models\Tag::forAccount($account->id)->orderBy('name')->get() as $tag)
                                <option value="{{ $tag->id }}">{{ $tag->name }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-sm btn-primary" id="bulk-apply-btn" disabled>
                            Apply
                        </button>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-secondary ms-auto" id="bulk-deselect-btn">
                        Deselect All
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-nowrap table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 40px;">
                                    <input type="checkbox" class="form-check-input" id="select-all">
                                </th>
                                <th>
                                    <a href="{{ route('transactions.index', array_merge(request()->all(), ['sort' => 'occurred_at', 'direction' => request('sort') == 'occurred_at' && request('direction') == 'desc' ? 'asc' : 'desc'])) }}" class="text-dark text-decoration-none">
                                        Date
                                        @if(request('sort') == 'occurred_at')
                                            <i data-feather="{{ request('direction') == 'asc' ? 'arrow-up' : 'arrow-down' }}" class="icon-xs"></i>
                                        @endif
                                    </a>
                                </th>
                                <th>
                                    <a href="{{ route('transactions.index', array_merge(request()->all(), ['sort' => 'type', 'direction' => request('sort') == 'type' && request('direction') == 'asc' ? 'desc' : 'asc'])) }}" class="text-dark text-decoration-none">
                                        Type
                                        @if(request('sort') == 'type')
                                            <i data-feather="{{ request('direction') == 'asc' ? 'arrow-up' : 'arrow-down' }}" class="icon-xs"></i>
                                        @endif
                                    </a>
                                </th>
                                <th>
                                    <a href="{{ route('transactions.index', array_merge(request()->all(), ['sort' => 'amount', 'direction' => request('sort') == 'amount' && request('direction') == 'asc' ? 'desc' : 'asc'])) }}" class="text-dark text-decoration-none">
                                        Amount
                                        @if(request('sort') == 'amount')
                                            <i data-feather="{{ request('direction') == 'asc' ? 'arrow-up' : 'arrow-down' }}" class="icon-xs"></i>
                                        @endif
                                    </a>
                                </th>
                                <th>Income Source</th>
                                <th>Expense Type</th>
                                <th>Note</th>
                                <th>Tags</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transactions as $transaction)
                                <tr>
                                    <td>
                                        <input type="checkbox" class="form-check-input bulk-check" value="{{ $transaction->id }}">
                                    </td>
                                    <td>{{ $transaction->occurred_at->format('M d, Y') }}</td>
                                    <td>
                                        <span class="badge bg-{{ $transaction->type === 'income' ? 'success' : ($transaction->type === 'expense' ? 'danger' : 'info') }}">
                                            {{ ucfirst($transaction->type) }}
                                        </span>
                                    </td>
                                    <td>
                                        <strong class="{{ $transaction->type === 'income' ? 'text-success' : 'text-danger' }}">
                                            {{ \App\Helpers\CurrencyHelper::format($transaction->amount, $currencyCode) }}
                                        </strong>
                                    </td>
                                    <td>{{ $transaction->wallet->name ?? 'N/A' }}</td>
                                    <td>{{ $transaction->category->name ?? 'N/A' }}</td>
                                    <td>{{ Str::limit($transaction->note, 30) }}</td>
                                    <td>
                                        @foreach($transaction->tags as $tag)
                                            <span class="badge" style="background-color: {{ $tag->color }}">{{ $tag->name }}</span>
                                        @endforeach
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('transactions.duplicate', $transaction) }}" class="btn btn-sm btn-outline-secondary" title="Duplicate">
                                            <i data-feather="copy" class="icon-xs"></i>
                                        </a>
                                        <a href="{{ route('transactions.edit', $transaction) }}" class="btn btn-sm btn-primary">
                                            <i data-feather="edit-2" class="icon-xs"></i> Edit
                                        </a>
                                        <button type="button" class="btn btn-sm btn-danger"
                                            data-bs-toggle="modal" data-bs-target="#confirmModal"
                                            data-action="{{ route('transactions.destroy', $transaction) }}"
                                            data-message="Are you sure you want to delete this transaction?">
                                            <i data-feather="trash-2" class="icon-xs"></i> Delete
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-5">
                                        <div class="text-muted">
                                            <i data-feather="arrow-left-right" class="icon-lg mb-2"></i>
                                            <p class="mb-2">No transactions found.</p>
                                            <a href="{{ route('transactions.create') }}" class="btn btn-primary">Add Your First Transaction</a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($transactions->hasPages())
                    <div class="d-flex justify-content-end mt-3">
                        {{ $transactions->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function() {
    var toolbar = document.getElementById('bulk-toolbar');
    var bulkForm = document.getElementById('bulk-form');
    var idsContainer = document.getElementById('bulk-ids-container');
    var countEl = document.getElementById('bulk-count');
    var selectAll = document.getElementById('select-all');
    var checkboxes = document.querySelectorAll('.bulk-check');
    var actionSelect = document.getElementById('bulk-action-select');
    var categorySelect = document.getElementById('bulk-category');
    var tagSelect = document.getElementById('bulk-tag');
    var applyBtn = document.getElementById('bulk-apply-btn');
    var deselectBtn = document.getElementById('bulk-deselect-btn');

    function updateToolbar() {
        var checked = document.querySelectorAll('.bulk-check:checked');
        var count = checked.length;
        countEl.textContent = count;
        toolbar.style.display = count > 0 ? '' : 'none';

        // Update select-all state
        selectAll.checked = count > 0 && count === checkboxes.length;
        selectAll.indeterminate = count > 0 && count < checkboxes.length;

        // Update hidden inputs
        idsContainer.innerHTML = '';
        checked.forEach(function(cb) {
            var input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'ids[]';
            input.value = cb.value;
            idsContainer.appendChild(input);
        });
    }

    selectAll.addEventListener('change', function() {
        checkboxes.forEach(function(cb) { cb.checked = selectAll.checked; });
        updateToolbar();
    });

    checkboxes.forEach(function(cb) {
        cb.addEventListener('change', updateToolbar);
    });

    deselectBtn.addEventListener('click', function() {
        checkboxes.forEach(function(cb) { cb.checked = false; });
        selectAll.checked = false;
        updateToolbar();
    });

    actionSelect.addEventListener('change', function() {
        var action = actionSelect.value;
        categorySelect.style.display = action === 'set_category' ? '' : 'none';
        tagSelect.style.display = (action === 'add_tag' || action === 'remove_tag') ? '' : 'none';
        applyBtn.disabled = !action;
    });

    bulkForm.addEventListener('submit', function(e) {
        var action = actionSelect.value;
        var count = document.querySelectorAll('.bulk-check:checked').length;
        if (action === 'delete' && !bulkForm.dataset.confirmed) {
            e.preventDefault();
            var modal = new bootstrap.Modal(document.getElementById('confirmModal'));
            document.getElementById('confirmModal-message').textContent = 'Are you sure you want to delete ' + count + ' transactions? This cannot be undone.';
            var confirmForm = document.getElementById('confirmModal-form');
            var confirmBtn = confirmForm.querySelector('button[type="submit"]');
            var newBtn = confirmBtn.cloneNode(true);
            confirmBtn.parentNode.replaceChild(newBtn, confirmBtn);
            newBtn.addEventListener('click', function(ev) {
                ev.preventDefault();
                bulkForm.dataset.confirmed = 'true';
                modal.hide();
                bulkForm.submit();
            });
            modal.show();
        }
        if (bulkForm.dataset.confirmed) {
            delete bulkForm.dataset.confirmed;
        }
    });
})();
</script>
@endpush

<x-confirm-modal />
@endsection
