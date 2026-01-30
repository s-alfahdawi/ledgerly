<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="theme-color" content="#405189">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="default">

        <title>@yield('title', config('app.name', 'Laravel'))</title>

        <link rel="manifest" href="{{ url('/manifest.webmanifest') }}">
        <link rel="apple-touch-icon" href="{{ asset('assets/minia/images/favicon.ico') }}">

        <!-- Bootstrap core CSS -->
        <link rel="stylesheet" href="{{ asset('assets/invoza/css/bootstrap.min.css') }}" type="text/css">

        <!-- Material Icon -->
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/invoza/css/materialdesignicons.min.css') }}" />

        <!-- Custom CSS -->
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/invoza/css/style.css') }}" />
        
        <style>
            body {
                background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
                min-height: 100vh;
            }
        </style>
    </head>
    <body>
        {{ $slot }}

        <!-- Javascript -->
        <script src="{{ asset('assets/invoza/js/jquery.min.js') }}"></script>
        <script src="{{ asset('assets/invoza/js/bootstrap.bundle.min.js') }}"></script>
        <script src="{{ asset('assets/invoza/js/feather.min.js') }}"></script>
        <script>
            feather.replace();
        </script>
        @if(config('app.env') !== 'local')
        <script>
            if ('serviceWorker' in navigator) {
                window.addEventListener('load', function () {
                    navigator.serviceWorker.register('{{ url("/sw.js") }}', { scope: '/' }).catch(function () {});
                });
            }
        </script>
        @endif
    </body>
</html>
