<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title', config('app.name'))</title>

    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">

    {{-- Bootstrap 5 CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Toastr CSS --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

    {{-- Custom CSS (Opsiyonel) --}}
    @stack('styles')
</head>
<body>
    {{-- Header --}}
    @include('layouts.header')

    <main class="container py-4">
        @yield('content')
    </main>

    {{-- Footer --}}
    @include('layouts.footer')

    {{-- jQuery --}}
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    {{-- Bootstrap 5 JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    {{-- Toastr JS --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    {{-- SweetAlert2 JS --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- Global Toastr Options --}}
    <script>
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "timeOut": "4000",
            "extendedTimeOut": "2000",
            "positionClass": "toast-top-right"
        };
    </script>

    {{-- Custom Scripts --}}
    @stack('scripts')
</body>
</html>
