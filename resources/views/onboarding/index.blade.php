<x-guest-layout>
@section('title', 'Get Started')

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-xl-7">

            {{-- Header --}}
            <div class="text-center mb-4">
                <h2 class="fw-bold" style="color: #405189;">Welcome to Ledgerly</h2>
                <p class="text-muted">Let's set up your account in a few quick steps</p>
            </div>

            {{-- Step Indicators --}}
            @php
                $step = session('step', 'account');
                if ($hasWallets && !$hasCategories) $step = 'categories';
                elseif (!$hasWallets && $step === 'account') $step = 'account';
            @endphp
            <div class="d-flex justify-content-center mb-4">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge rounded-pill {{ $step === 'account' ? 'bg-primary' : ($step === 'wallets' || $step === 'categories' ? 'bg-success' : 'bg-secondary') }}" style="width: 28px; height: 28px; line-height: 20px;">1</span>
                    <span class="small {{ $step === 'account' ? 'fw-bold' : 'text-muted' }}">Account</span>
                    <div style="width: 40px; height: 2px; background: #dee2e6;"></div>
                    <span class="badge rounded-pill {{ $step === 'wallets' ? 'bg-primary' : ($step === 'categories' ? 'bg-success' : 'bg-secondary') }}" style="width: 28px; height: 28px; line-height: 20px;">2</span>
                    <span class="small {{ $step === 'wallets' ? 'fw-bold' : 'text-muted' }}">Wallets</span>
                    <div style="width: 40px; height: 2px; background: #dee2e6;"></div>
                    <span class="badge rounded-pill {{ $step === 'categories' ? 'bg-primary' : 'bg-secondary' }}" style="width: 28px; height: 28px; line-height: 20px;">3</span>
                    <span class="small {{ $step === 'categories' ? 'fw-bold' : 'text-muted' }}">Categories</span>
                </div>
            </div>

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- Step 1: Account Setup --}}
            @if($step === 'account')
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <h5 class="card-title mb-1">Step 1: Account Details</h5>
                    <p class="text-muted small mb-4">Confirm your account name, currency, and timezone.</p>

                    <form method="POST" action="{{ route('onboarding.account') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-medium">Account Name</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $account->name) }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-medium">Currency</label>
                            <select name="currency_code" class="form-select" required>
                                <option value="IQD" {{ $account->currency_code === 'IQD' ? 'selected' : '' }}>IQD - Iraqi Dinar</option>
                                <option value="USD" {{ $account->currency_code === 'USD' ? 'selected' : '' }}>USD - US Dollar</option>
                                <option value="EUR" {{ $account->currency_code === 'EUR' ? 'selected' : '' }}>EUR - Euro</option>
                                <option value="GBP" {{ $account->currency_code === 'GBP' ? 'selected' : '' }}>GBP - British Pound</option>
                                <option value="AED" {{ $account->currency_code === 'AED' ? 'selected' : '' }}>AED - UAE Dirham</option>
                                <option value="SAR" {{ $account->currency_code === 'SAR' ? 'selected' : '' }}>SAR - Saudi Riyal</option>
                                <option value="TRY" {{ $account->currency_code === 'TRY' ? 'selected' : '' }}>TRY - Turkish Lira</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-medium">Timezone</label>
                            <select name="timezone" class="form-select" required>
                                @foreach(['Asia/Baghdad', 'UTC', 'America/New_York', 'America/Chicago', 'America/Los_Angeles', 'Europe/London', 'Europe/Paris', 'Europe/Istanbul', 'Asia/Dubai', 'Asia/Riyadh', 'Asia/Kolkata', 'Asia/Tokyo'] as $tz)
                                    <option value="{{ $tz }}" {{ ($account->timezone ?? 'Asia/Baghdad') === $tz ? 'selected' : '' }}>{{ $tz }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('onboarding.skip') }}" class="btn btn-link text-muted">Skip setup</a>
                            <button type="submit" class="btn btn-primary">
                                Next: Add Wallets <i data-feather="arrow-right" class="align-middle ms-1" style="width:16px;height:16px;"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            @endif

            {{-- Step 2: Wallets --}}
            @if($step === 'wallets')
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <h5 class="card-title mb-1">Step 2: Income Sources (Wallets)</h5>
                    <p class="text-muted small mb-4">Where does your money come in and go out? Add at least one wallet.</p>

                    <form method="POST" action="{{ route('onboarding.wallets') }}" id="walletsForm">
                        @csrf

                        {{-- Preset quick-add buttons --}}
                        <div class="mb-3">
                            <label class="form-label fw-medium">Quick Add</label>
                            <div class="d-flex flex-wrap gap-2">
                                <button type="button" class="btn btn-sm btn-outline-primary preset-wallet" data-name="Cash" data-type="cash">+ Cash</button>
                                <button type="button" class="btn btn-sm btn-outline-primary preset-wallet" data-name="Bank Account" data-type="bank">+ Bank Account</button>
                                <button type="button" class="btn btn-sm btn-outline-primary preset-wallet" data-name="Mobile Money" data-type="mobile_money">+ Mobile Money</button>
                                <button type="button" class="btn btn-sm btn-outline-primary preset-wallet" data-name="Savings" data-type="bank">+ Savings</button>
                                <button type="button" class="btn btn-sm btn-outline-primary preset-wallet" data-name="Credit Card" data-type="other">+ Credit Card</button>
                            </div>
                        </div>

                        <div id="walletsList">
                            {{-- Wallet entries added dynamically --}}
                        </div>

                        <button type="button" class="btn btn-sm btn-outline-secondary mb-3" id="addCustomWallet">
                            <i data-feather="plus" style="width:14px;height:14px;" class="align-middle"></i> Add Custom Wallet
                        </button>

                        <div id="noWalletsMsg" class="alert alert-info small">
                            <i data-feather="info" style="width:14px;height:14px;" class="align-middle me-1"></i>
                            Click the presets above or add a custom wallet to get started.
                        </div>

                        <div class="d-flex justify-content-between mt-3">
                            <a href="{{ route('onboarding.skip') }}" class="btn btn-link text-muted">Skip setup</a>
                            <button type="submit" class="btn btn-primary" id="walletsSubmit" disabled>
                                Next: Add Categories <i data-feather="arrow-right" class="align-middle ms-1" style="width:16px;height:16px;"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            @endif

            {{-- Step 3: Categories --}}
            @if($step === 'categories')
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <h5 class="card-title mb-1">Step 3: Expense & Income Types</h5>
                    <p class="text-muted small mb-4">How do you classify your spending and earnings? Add at least one category.</p>

                    <form method="POST" action="{{ route('onboarding.categories') }}" id="categoriesForm">
                        @csrf

                        <h6 class="text-muted mb-2">Expense Categories</h6>
                        <div class="d-flex flex-wrap gap-2 mb-3">
                            <button type="button" class="btn btn-sm btn-outline-danger preset-cat" data-name="Food & Dining" data-type="expense" data-color="#f06548">+ Food & Dining</button>
                            <button type="button" class="btn btn-sm btn-outline-danger preset-cat" data-name="Transport" data-type="expense" data-color="#f7b84b">+ Transport</button>
                            <button type="button" class="btn btn-sm btn-outline-danger preset-cat" data-name="Rent" data-type="expense" data-color="#405189">+ Rent</button>
                            <button type="button" class="btn btn-sm btn-outline-danger preset-cat" data-name="Utilities" data-type="expense" data-color="#299cdb">+ Utilities</button>
                            <button type="button" class="btn btn-sm btn-outline-danger preset-cat" data-name="Shopping" data-type="expense" data-color="#e83e8c">+ Shopping</button>
                            <button type="button" class="btn btn-sm btn-outline-danger preset-cat" data-name="Health" data-type="expense" data-color="#0ab39c">+ Health</button>
                            <button type="button" class="btn btn-sm btn-outline-danger preset-cat" data-name="Entertainment" data-type="expense" data-color="#6610f2">+ Entertainment</button>
                        </div>

                        <h6 class="text-muted mb-2">Income Categories</h6>
                        <div class="d-flex flex-wrap gap-2 mb-3">
                            <button type="button" class="btn btn-sm btn-outline-success preset-cat" data-name="Salary" data-type="income" data-color="#0ab39c">+ Salary</button>
                            <button type="button" class="btn btn-sm btn-outline-success preset-cat" data-name="Freelance" data-type="income" data-color="#299cdb">+ Freelance</button>
                            <button type="button" class="btn btn-sm btn-outline-success preset-cat" data-name="Business" data-type="income" data-color="#405189">+ Business</button>
                            <button type="button" class="btn btn-sm btn-outline-success preset-cat" data-name="Investments" data-type="income" data-color="#f7b84b">+ Investments</button>
                        </div>

                        <div id="categoriesList">
                            {{-- Category entries added dynamically --}}
                        </div>

                        <button type="button" class="btn btn-sm btn-outline-secondary mb-3" id="addCustomCategory">
                            <i data-feather="plus" style="width:14px;height:14px;" class="align-middle"></i> Add Custom Category
                        </button>

                        <div id="noCatsMsg" class="alert alert-info small">
                            <i data-feather="info" style="width:14px;height:14px;" class="align-middle me-1"></i>
                            Click the presets above or add a custom category to get started.
                        </div>

                        <div class="d-flex justify-content-between mt-3">
                            <a href="{{ route('onboarding.skip') }}" class="btn btn-link text-muted">Skip setup</a>
                            <button type="submit" class="btn btn-success" id="catsSubmit" disabled>
                                <i data-feather="check" class="align-middle me-1" style="width:16px;height:16px;"></i> Finish Setup
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            @endif

        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // -- Wallets Step --
    var walletIndex = 0;
    var walletsList = document.getElementById('walletsList');
    var noWalletsMsg = document.getElementById('noWalletsMsg');
    var walletsSubmit = document.getElementById('walletsSubmit');

    function addWallet(name, type, balance) {
        var idx = walletIndex++;
        var div = document.createElement('div');
        div.className = 'card bg-light mb-2';
        div.id = 'wallet-' + idx;
        div.innerHTML = '<div class="card-body p-3">' +
            '<div class="row g-2 align-items-end">' +
                '<div class="col-md-4">' +
                    '<label class="form-label small mb-1">Name</label>' +
                    '<input type="text" name="wallets[' + idx + '][name]" class="form-control form-control-sm" value="' + name + '" required>' +
                '</div>' +
                '<div class="col-md-3">' +
                    '<label class="form-label small mb-1">Type</label>' +
                    '<select name="wallets[' + idx + '][type]" class="form-select form-select-sm" required>' +
                        '<option value="cash"' + (type === 'cash' ? ' selected' : '') + '>Cash</option>' +
                        '<option value="bank"' + (type === 'bank' ? ' selected' : '') + '>Bank</option>' +
                        '<option value="mobile_money"' + (type === 'mobile_money' ? ' selected' : '') + '>Mobile Money</option>' +
                        '<option value="other"' + (type === 'other' ? ' selected' : '') + '>Other</option>' +
                    '</select>' +
                '</div>' +
                '<div class="col-md-3">' +
                    '<label class="form-label small mb-1">Opening Balance</label>' +
                    '<input type="number" name="wallets[' + idx + '][opening_balance]" class="form-control form-control-sm" value="' + (balance || 0) + '" min="0" step="0.01">' +
                '</div>' +
                '<div class="col-md-2">' +
                    '<button type="button" class="btn btn-sm btn-outline-danger w-100 remove-wallet" data-id="wallet-' + idx + '">Remove</button>' +
                '</div>' +
            '</div>' +
        '</div>';
        walletsList.appendChild(div);
        updateWalletsState();
    }

    function updateWalletsState() {
        var count = walletsList ? walletsList.querySelectorAll('.card').length : 0;
        if (noWalletsMsg) noWalletsMsg.style.display = count > 0 ? 'none' : 'block';
        if (walletsSubmit) walletsSubmit.disabled = count === 0;
    }

    if (walletsList) {
        document.querySelectorAll('.preset-wallet').forEach(function (btn) {
            btn.addEventListener('click', function () {
                addWallet(this.dataset.name, this.dataset.type, 0);
                this.disabled = true;
                this.classList.remove('btn-outline-primary');
                this.classList.add('btn-primary');
            });
        });

        document.getElementById('addCustomWallet').addEventListener('click', function () {
            addWallet('', 'cash', 0);
        });

        walletsList.addEventListener('click', function (e) {
            if (e.target.classList.contains('remove-wallet')) {
                var el = document.getElementById(e.target.dataset.id);
                if (el) el.remove();
                updateWalletsState();
            }
        });
    }

    // -- Categories Step --
    var catIndex = 0;
    var categoriesList = document.getElementById('categoriesList');
    var noCatsMsg = document.getElementById('noCatsMsg');
    var catsSubmit = document.getElementById('catsSubmit');

    function addCategory(name, type, color) {
        var idx = catIndex++;
        var div = document.createElement('div');
        div.className = 'card bg-light mb-2';
        div.id = 'cat-' + idx;
        div.innerHTML = '<div class="card-body p-3">' +
            '<div class="row g-2 align-items-end">' +
                '<div class="col-md-4">' +
                    '<label class="form-label small mb-1">Name</label>' +
                    '<input type="text" name="categories[' + idx + '][name]" class="form-control form-control-sm" value="' + name + '" required>' +
                '</div>' +
                '<div class="col-md-3">' +
                    '<label class="form-label small mb-1">Type</label>' +
                    '<select name="categories[' + idx + '][type]" class="form-select form-select-sm" required>' +
                        '<option value="expense"' + (type === 'expense' ? ' selected' : '') + '>Expense</option>' +
                        '<option value="income"' + (type === 'income' ? ' selected' : '') + '>Income</option>' +
                        '<option value="both"' + (type === 'both' ? ' selected' : '') + '>Both</option>' +
                    '</select>' +
                '</div>' +
                '<div class="col-md-3">' +
                    '<label class="form-label small mb-1">Color</label>' +
                    '<input type="color" name="categories[' + idx + '][color]" class="form-control form-control-sm form-control-color" value="' + (color || '#405189') + '">' +
                '</div>' +
                '<div class="col-md-2">' +
                    '<button type="button" class="btn btn-sm btn-outline-danger w-100 remove-cat" data-id="cat-' + idx + '">Remove</button>' +
                '</div>' +
            '</div>' +
        '</div>';
        categoriesList.appendChild(div);
        updateCatsState();
    }

    function updateCatsState() {
        var count = categoriesList ? categoriesList.querySelectorAll('.card').length : 0;
        if (noCatsMsg) noCatsMsg.style.display = count > 0 ? 'none' : 'block';
        if (catsSubmit) catsSubmit.disabled = count === 0;
    }

    if (categoriesList) {
        document.querySelectorAll('.preset-cat').forEach(function (btn) {
            btn.addEventListener('click', function () {
                addCategory(this.dataset.name, this.dataset.type, this.dataset.color);
                this.disabled = true;
                if (this.dataset.type === 'expense') {
                    this.classList.remove('btn-outline-danger');
                    this.classList.add('btn-danger');
                } else {
                    this.classList.remove('btn-outline-success');
                    this.classList.add('btn-success');
                }
            });
        });

        document.getElementById('addCustomCategory').addEventListener('click', function () {
            addCategory('', 'expense', '#405189');
        });

        categoriesList.addEventListener('click', function (e) {
            if (e.target.classList.contains('remove-cat')) {
                var el = document.getElementById(e.target.dataset.id);
                if (el) el.remove();
                updateCatsState();
            }
        });
    }

    // Re-initialize feather icons for dynamically added elements
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
});
</script>

</x-guest-layout>
