<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }} - Super Admin @yield('title')</title>
    @vite(['resources/css/admin.css', 'resources/js/admin/admin.js'])
    <script>
        // Localized validation messages
        window.validationMessages = {!! json_encode(trans('admin.validation')) !!};
    </script>
</head>
<body class="h-full">
    <div class="flex min-h-full flex-col justify-center py-4 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <img class="mx-auto w-80" src="{{ asset('images/poolroute-logo.png') }}" alt="Pool Routes Logo">
             @yield('subheading')
        </div>
        @yield('content')
    </div>
    @livewireScripts
</body>
</html>
