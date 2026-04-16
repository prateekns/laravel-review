<!DOCTYPE html>
<html lang="en" class="h-full bg-white">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title> @yield('title', 'Business Portal')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        // Localized validation messages
        window.validationMessages = {!! json_encode(trans('validation')) !!};
        window.validation = {!! json_encode(trans('common.auth')) !!};
    </script>
</head>
<body class=" buisness-onboarding">
    <div class="justify-center py-4 sm:px-6 lg:px-8">
        @yield('content')
    </div>
    @livewireScripts
    @yield('scripts')
</body>
</html>
