<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-50">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Pool Route</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>

    <body class="h-full">
        <div class="pool-home-page">
            <div class="pool-home-wrapper">
                <div class="home-page-logo-mobile">
                    <img src="{{ asset('images/home-page-logo.svg') }}" alt="Pool Route Logo">
                </div>
                <div class="pool-home-left-box">
                    <img src="{{ asset('images/home-catlog-image.png') }}" alt="Welcome to Business Platform">
                </div>

                <!-- Right side with content -->
                <div class="pool-home-right-box">
                    <div class="text-center">
                        <div class="home-page-logo">
                            <img src="{{ asset('images/home-page-logo.svg') }}" alt="Pool Route Logo">
                        </div>
                        <div class="home-page-text-box">
                            <h1>{{ __('Your Pool Service Perfected –') }}</h1>
                            <p>
                                {{__('The all-in-one platform that helps pool service businesses manage customers, jobs with ease')}}.
                            </p>
                        </div>
                    </div>

                    <div class="home-login-box">
                        <a href="{{ route('login') }}" class="login-btn">
                            {{__('Login')}}
                        </a>
                        <a href="{{ route('register') }}" class="create-acct-btn">
                            {{__('Create account')}}
                        </a>
                    </div>

                    <div class="download-app-box">
                        <div class="download-app-tag">
                            <hr>
                            <span class="px-2 text-sm text-gray-500 whitespace-nowrap">{{__('Download our Mobile App')}}</span>
                        </div>

                        <div class="flex justify-center space-x-4">
                            <a href="#" class="inline-block">
                                <img src="{{ asset('images/google-play-badge.svg') }}" alt="Get it on Google Play" class="h-12">
                            </a>
                            <a href="#" class="inline-block">
                                <img src="{{ asset('images/app-store-badge.svg') }}" alt="Download on the App Store" class="h-12">
                            </a>
                        </div>

                        <a href="/privacy-policy" class="inline-block"><div class="flex justify-center p-4">Privacy Policy</div></a>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
