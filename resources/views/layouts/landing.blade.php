<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Ledgerly - Smart Personal Finance Management')</title>
    <meta name="description" content="Ledgerly: Modern personal finance management app. Track income, expenses, manage income sources, visualize spending with charts, and collaborate with your team. Free and open-source." />
    <meta name="keywords" content="ledgerly, personal finance, expense tracking, income management, financial dashboard, budgeting, accounting, finance app" />
    <meta name="theme-color" content="#405189">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">

    <!-- PWA manifest -->
    <link rel="manifest" href="{{ url('/manifest.webmanifest') }}">
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('assets/invoza/images/favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('assets/invoza/images/favicon.ico') }}">

    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" href="{{ asset('assets/invoza/css/bootstrap.min.css') }}" type="text/css">

    <!-- Material Icon -->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/invoza/css/materialdesignicons.min.css') }}" />

    <!-- owl carousel -->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/invoza/css/owl.carousel.min.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/invoza/css/owl.theme.default.min.css') }}" />

    <!-- Custom CSS -->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/invoza/css/style.css') }}" />

</head>

<body>
    @yield('content')

    <!-- Javascript -->
    <script src="{{ asset('assets/invoza/js/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/invoza/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/invoza/js/jquery.easing.min.js') }}"></script>
    <script src="{{ asset('assets/invoza/js/scrollspy.min.js') }}"></script>
    <script src="{{ asset('assets/invoza/js/feather.min.js') }}"></script>

    <!-- owl carousel -->
    <script src="{{ asset('assets/invoza/js/owl.carousel.min.js') }}"></script>

    <!-- app js -->
    <script src="{{ asset('assets/invoza/js/app.js') }}"></script>

    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function () {
                navigator.serviceWorker.register('{{ url("/sw.js") }}', { scope: '/' }).catch(function () {});
            });
        }
    </script>

    @stack('scripts')

</body>

</html>
