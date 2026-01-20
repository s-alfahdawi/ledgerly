<header id="page-topbar">
    <div class="navbar-header">
        <div class="d-flex">
            <!-- LOGO -->
            <div class="navbar-brand-box">
                <a href="{{ route('dashboard') }}" class="logo logo-dark">
                    <span class="logo-sm">
                        <img src="{{ asset('assets/minia/images/logo-sm.svg') }}" alt="" height="24">
                    </span>
                    <span class="logo-lg">
                        <img src="{{ asset('assets/minia/images/logo-sm.svg') }}" alt="" height="24"> <span class="logo-txt">Billing</span>
                    </span>
                </a>

                <a href="{{ route('dashboard') }}" class="logo logo-light">
                    <span class="logo-sm">
                        <img src="{{ asset('assets/minia/images/logo-sm.svg') }}" alt="" height="24">
                    </span>
                    <span class="logo-lg">
                        <img src="{{ asset('assets/minia/images/logo-sm.svg') }}" alt="" height="24"> <span class="logo-txt">Billing</span>
                    </span>
                </a>
            </div>

            <button type="button" class="btn btn-sm px-3 font-size-16 header-item" id="vertical-menu-btn">
                <i class="fa fa-fw fa-bars"></i>
            </button>

            <!-- App Search-->
            <form class="app-search d-none d-lg-block">
                <div class="position-relative">
                    <input type="text" class="form-control" placeholder="Search...">
                    <button class="btn btn-primary" type="button"><i class="bx bx-search-alt align-middle"></i></button>
                </div>
            </form>
        </div>

        <div class="d-flex">
            <!-- Account Switcher -->
            @php
                $accountContext = app(\App\Services\AccountContext::class);
                $account = $accountContext->account();
                $userAccounts = auth()->user()->accounts;
            @endphp
            @if($userAccounts->count() > 1)
                <div class="dropdown d-inline-block me-2">
                    <button type="button" class="btn header-item" data-bs-toggle="dropdown">
                        <i class="mdi mdi-wallet me-1"></i>
                        <span class="d-none d-xl-inline-block ms-1">{{ $account?->name ?? 'Select Account' }}</span>
                        <i class="mdi mdi-chevron-down d-none d-xl-inline-block ms-1"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end">
                        @foreach($userAccounts as $acc)
                            <form method="POST" action="{{ route('accounts.switch') }}" class="d-inline">
                                @csrf
                                <input type="hidden" name="account_id" value="{{ $acc->id }}">
                                <button type="submit" class="dropdown-item {{ $account && $account->id === $acc->id ? 'active' : '' }}">
                                    <i class="mdi mdi-wallet me-2"></i>{{ $acc->name }}
                                </button>
                            </form>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="dropdown d-none d-sm-inline-block">
                <button type="button" class="btn header-item" id="mode-setting-btn">
                    <i data-feather="moon" class="icon-lg layout-mode-dark"></i>
                    <i data-feather="sun" class="icon-lg layout-mode-light"></i>
                </button>
            </div>

            <div class="dropdown d-inline-block">
                <button type="button" class="btn header-item bg-light-subtle border-start border-end" id="page-header-user-dropdown"
                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <span class="d-none d-xl-inline-block ms-1 fw-medium">{{ auth()->user()->name }}</span>
                    <i class="mdi mdi-chevron-down d-none d-xl-inline-block"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-end">
                    <!-- item-->
                    <a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="mdi mdi-face-man font-size-16 align-middle me-1"></i> Profile</a>
                    @php
                        $accountContext = app(\App\Services\AccountContext::class);
                        $accountId = $accountContext->id();
                        $user = auth()->user();
                    @endphp
                    @if($accountId && $user->hasPermissionInAccount($accountId, 'settings.view'))
                    <a class="dropdown-item" href="{{ route('settings.index') }}"><i class="mdi mdi-cog font-size-16 align-middle me-1"></i> Settings</a>
                    @endif
                    <div class="dropdown-divider"></div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item"><i class="mdi mdi-logout font-size-16 align-middle me-1"></i> Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>

<!-- ========== Left Sidebar Start ========== -->
<div class="vertical-menu">
    <div data-simplebar class="h-100">
        <!--- Sidemenu -->
        <div id="sidebar-menu">
            <!-- Left Menu Start -->
            <ul class="metismenu list-unstyled" id="side-menu">
                <li class="menu-title" data-key="t-menu">Menu</li>

                <li>
                    <a href="{{ route('dashboard') }}">
                        <i data-feather="home"></i>
                        <span data-key="t-dashboard">Dashboard</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('transactions.index') }}">
                        <i data-feather="repeat"></i>
                        <span>Transactions</span>
                    </a>
                </li>

                @php
                    $accountContext = app(\App\Services\AccountContext::class);
                    $accountId = $accountContext->id();
                    $user = auth()->user();
                @endphp

                @if($accountId && $user->hasPermissionInAccount($accountId, 'wallets.view'))
                <li>
                    <a href="{{ route('wallets.index') }}">
                        <i data-feather="trending-up"></i>
                        <span>Income Sources</span>
                    </a>
                </li>
                @endif

                @if($accountId && $user->hasPermissionInAccount($accountId, 'categories.view'))
                <li>
                    <a href="{{ route('categories.index') }}">
                        <i data-feather="tag"></i>
                        <span>Expense Types</span>
                    </a>
                </li>
                @endif

                <li>
                    <a href="{{ route('reports.index') }}">
                        <i data-feather="bar-chart-2"></i>
                        <span>Reports</span>
                    </a>
                </li>

                @if($accountId && $user->hasPermissionInAccount($accountId, 'members.view'))
                <li>
                    <a href="{{ route('members.index') }}">
                        <i data-feather="users"></i>
                        <span>Members</span>
                    </a>
                </li>
                @endif

                @if($accountId && $user->hasPermissionInAccount($accountId, 'settings.view'))
                <li>
                    <a href="{{ route('settings.index') }}">
                        <i data-feather="settings"></i>
                        <span>Settings</span>
                    </a>
                </li>
                @endif
            </ul>
        </div>
        <!-- Sidebar -->
    </div>
</div>
<!-- Left Sidebar End -->
