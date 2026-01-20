<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', config('app.name', 'Laravel'))</title>

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
    </body>
</html>
