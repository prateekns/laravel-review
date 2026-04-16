<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-50">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} - {{__('Business Dashboard')}} - @yield('title', '')</title>
    <link rel="icon" sizes="32x32" href="{{ asset('favicon-32x32.png') }}" type="image/png">
    <link rel="icon" sizes="16x16" href="{{ asset('favicon-16x16.png') }}" type="image/png">
    <link rel="icon" sizes="192x192" href="{{ asset('android-chrome-192x192.png') }}" type="image/png">
    <link rel="icon" sizes="512x512" href="{{ asset('android-chrome-512x512.png') }}" type="image/png">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    @livewireStyles
    <script>
        // Localized error messages
        window.paymentMessages = {!!json_encode(trans('payments')) !!};
        window.validation = {!!json_encode(trans('common.auth')) !!};
        window.stripeKey = "{{ config('services.stripe.key') }}";
    </script>
    <script src="https://js.stripe.com/v3/"></script>
    @yield('styles')
</head>

<body class="h-full font-geist">
    <div class="body-wrapper">
        <div class="bg-grey w-full h-full">
            
            @auth('business')
                @include('layouts.business.partials.header')
            @endauth

            <main class="main-wrapper">
                @if (session()->has('notification'))
                    <x-notification-alert
                        type="{{ session('notification.type') }}"
                        message="{{ session('notification.message') }}"
                        :timestamp="now()"
                    />
                @endif

                <div class="px-4 sm:px-6 lg:px-[30px]">
                    @yield('content')
                </div>

            </main>
            <livewire:business.footer />
        </div>
    </div>

    @yield('scripts')
    @livewireScripts
</body>

</html>
