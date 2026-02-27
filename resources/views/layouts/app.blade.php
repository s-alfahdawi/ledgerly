<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>@yield('title', 'Dashboard') | Billing App</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Personal Billing and Accounting Application" name="description" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#405189">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    
    <!-- PWA manifest -->
    <link rel="manifest" href="{{ url('/manifest.webmanifest') }}">
    <!-- App favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.svg') }}">
    <link rel="apple-touch-icon" href="{{ asset('favicon.svg') }}">

    <!-- preloader css -->
    <link rel="stylesheet" href="{{ asset('assets/minia/css/preloader.min.css') }}" type="text/css" />

    <!-- Bootstrap Css -->
    <link href="{{ asset('assets/minia/css/bootstrap.min.css') }}" id="bootstrap-style" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="{{ asset('assets/minia/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href="{{ asset('assets/minia/css/app.min.css') }}" id="app-style" rel="stylesheet" type="text/css" />
    <!-- Loading skeletons -->
    <link href="{{ asset('css/loading-skeleton.css') }}" rel="stylesheet" type="text/css" />
    <!-- Choices.js (searchable selects) -->
    <link href="{{ asset('assets/minia/libs/choices.js/public/assets/styles/choices.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Mobile bottom nav -->
    <link href="{{ asset('css/mobile-nav.css') }}" rel="stylesheet" type="text/css" />

</head>

<script>
    // Apply dark mode preference immediately to prevent FOUC
    (function() {
        var theme = localStorage.getItem('ledgerly-theme');
        if (theme === 'dark') {
            document.documentElement.setAttribute('data-bs-theme', 'dark');
        }
    })();
</script>
<body>
    <!-- Begin page -->
    <div id="layout-wrapper">
        @include('components.app-layout')
        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    @yield('content')
                </div>
            </div>
            <!-- End Page-content -->
            @include('components.footer')
        </div>
        <!-- end main content-->
    </div>
    <!-- END layout-wrapper -->

    <!-- Mobile Bottom Navigation -->
    <nav class="mobile-bottom-nav">
        <div class="nav-item">
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                <span>Home</span>
            </a>
        </div>
        <div class="nav-item">
            <a href="{{ route('transactions.index') }}" class="nav-link {{ request()->routeIs('transactions.*') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="17 1 21 5 17 9"/><path d="M3 11V9a4 4 0 0 1 4-4h14"/><polyline points="7 23 3 19 7 15"/><path d="M21 13v2a4 4 0 0 1-4 4H3"/></svg>
                <span>Transactions</span>
            </a>
        </div>
        <div class="nav-item">
            <a href="{{ route('transactions.create') }}" class="nav-link nav-add">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            </a>
        </div>
        <div class="nav-item">
            <a href="{{ route('reports.index') }}" class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
                <span>Reports</span>
            </a>
        </div>
        <div class="nav-item">
            <a href="{{ route('budgets.index') }}" class="nav-link {{ request()->routeIs('budgets.*') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M8 14s1.5 2 4 2 4-2 4-2"/><line x1="9" y1="9" x2="9.01" y2="9"/><line x1="15" y1="9" x2="15.01" y2="9"/></svg>
                <span>Budgets</span>
            </a>
        </div>
    </nav>

    <!-- JAVASCRIPT -->
    <script src="{{ asset('assets/minia/libs/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/minia/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/minia/libs/metismenu/metisMenu.min.js') }}"></script>
    <script src="{{ asset('assets/minia/libs/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ asset('assets/minia/libs/node-waves/waves.min.js') }}"></script>
    <script src="{{ asset('assets/minia/libs/feather-icons/feather.min.js') }}"></script>
    <!-- pace js -->
    <script src="{{ asset('assets/minia/libs/pace-js/pace.min.js') }}"></script>

    <!-- Apexcharts -->
    <script src="{{ asset('assets/minia/libs/apexcharts/apexcharts.min.js') }}"></script>

    <!-- dashboard init (only load on dashboard page) -->
    @if(request()->routeIs('dashboard'))
    <script src="{{ asset('assets/minia/js/pages/dashboard.init.js') }}"></script>
    @endif

    <script src="{{ asset('assets/minia/js/app.js') }}"></script>

    <!-- Initialize Feather Icons -->
    <script>
        feather.replace();
    </script>

    <!-- Dark mode persistence -->
    <script>
    (function() {
        var theme = localStorage.getItem('ledgerly-theme');
        if (theme === 'dark') {
            document.body.setAttribute('data-bs-theme', 'dark');
            document.body.setAttribute('data-topbar', 'dark');
            document.body.setAttribute('data-sidebar', 'dark');
        }

        var btn = document.getElementById('mode-setting-btn');
        if (btn) {
            btn.addEventListener('click', function() {
                setTimeout(function() {
                    var current = document.body.getAttribute('data-bs-theme');
                    localStorage.setItem('ledgerly-theme', current === 'dark' ? 'dark' : 'light');
                }, 50);
            });
        }
    })();
    </script>

    <!-- PWA: Register service worker -->
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function () {
                navigator.serviceWorker.register('{{ url("/sw.js") }}', { scope: '/' })
                    .then(function (reg) { /* optional: reg.update(); */ })
                    .catch(function () {});
            });
        }
    </script>
    
    <!-- Choices.js (searchable selects) -->
    <script src="{{ asset('assets/minia/libs/choices.js/public/assets/scripts/choices.min.js') }}"></script>
    <script src="{{ asset('js/searchable-selects.js') }}"></script>
    <!-- Client-side form validation -->
    <script src="{{ asset('js/form-validation.js') }}"></script>
    <!-- Keyboard shortcuts -->
    <script src="{{ asset('js/keyboard-shortcuts.js') }}"></script>

    @stack('scripts')

</body>

</html>
