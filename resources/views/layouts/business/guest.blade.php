<!DOCTYPE html>
<html lang="en" class="h-full bg-white">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }} - @yield('title', 'Business Portal')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        // Localized validation messages
        window.validationMessages = {!!json_encode(trans('validation')) !!};
        window.validation = {!!json_encode(trans('common.auth')) !!};
    </script>
</head>

<body class="h-full @yield('body_class')">
    <div class="Register-page">
        <div class="register-bg">
            <img src="{{ asset('images/register-bg-image.jpg') }}" alt="{{ config('app.name') }} Register BG" class="backround-image">
            <div class="register-bg-overlay">
                <div class="logo">
                    <img src="{{ asset('images/poolside-transparent-logo.svg') }}" alt="{{ config('app.name') }} Logo">
                    
                </div>
                <p>{{ __('Your Pool Service Perfected') }}</p>
            </div>
        </div>
        <div class="Register-form">
            <img class="form-logo" src="{{ asset('images/PoolRoute-logo-solid.svg') }}" alt="{{ config('app.name') }} Logo">
            @hasSection('heading')
            <h2 >@yield('heading', 'Pool Service')</h2>
            @endif
            @yield('subheading')
            @yield('content')
        </div>
    </div>
    @yield('scripts')
    @livewireScripts
</body>

</html>
