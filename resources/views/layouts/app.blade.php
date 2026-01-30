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
    <link rel="shortcut icon" href="{{ asset('assets/minia/images/favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('assets/minia/images/favicon.ico') }}">

    <!-- preloader css -->
    <link rel="stylesheet" href="{{ asset('assets/minia/css/preloader.min.css') }}" type="text/css" />

    <!-- Bootstrap Css -->
    <link href="{{ asset('assets/minia/css/bootstrap.min.css') }}" id="bootstrap-style" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="{{ asset('assets/minia/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href="{{ asset('assets/minia/css/app.min.css') }}" id="app-style" rel="stylesheet" type="text/css" />

</head>

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
    
    @stack('scripts')

</body>

</html>
