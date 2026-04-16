<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-gray-50">
    <div class="w-full flex flex-col items-center pt-8">
        <img src="{{ asset('images/home-page-logo.svg') }}" alt="{{ config('app.name') }} Logo"
            class="mb-8 w-48 h-auto mx-auto" />
        <div class="w-full max-w-5xl bg-white rounded-xl shadow-xs p-4">
            @yield('content')
        </div>
    </div>
</body>

</html>
