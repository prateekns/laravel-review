@if(session('impersonated'))
    <div class="flex justify-center p-3 text-sm text-yellow-800 rounded-lg bg-yellow-50 dark:bg-gray-800 dark:text-yellow-300">
        <x-icons name="alert" class="h-5 w-5 mt-1"/>
        <div class="text-sm"> {{ __('business.login_as', [ 'business' => auth()->guard('business')->user()->business->name]) }}</div>
    </div>
@endif

<!-- Header -->
<header class="header">
    <div class="hamburger-icon">
            <img src="{{ asset('images/hamburger-icon.svg') }}" alt="hamburger-icon">
        </div>
    <div class="w-full  mx-auto flex justify-end items-center relative">
        
        <!-- Content -->
        <div class="flex items-center gap-4">

            <!-- Subscription Banner -->
            @include('layouts.business.partials.subscription-banner')
            <!-- Subscription Banner -->
             
            <!-- Profile dropdown -->
            <div class="flex items-center gap-4" x-data="{ open: false }" @click.away="open = false" @keydown.escape.window="open = false" class="relative">
                 <!-- User Avatar -->
                <div class="w-10 h-10 rounded-full overflow-hidden"  x-data="{ previewUrl:  @js(auth()->user()?->business->business_logo) }"
                 x-on:logo-uploaded.window="previewUrl = $event.detail">
                    <img x-show="previewUrl" x-bind:src="previewUrl" alt="User avatar" class="w-full h-full object-cover">
                    <div x-show="!previewUrl" class="w-full h-full bg-gray-200 flex items-center justify-center">
                        <span class="text-gray-600 text-[14px] font-medium">
                            {{ auth()->user()?->business->user_initials }}
                        </span>
                    </div>
                </div>
                <button @click="open = !open"
                        type="button"
                        class="flex items-center cursor-pointer"
                        id="user-menu-button"
                        :aria-expanded="open"
                        aria-haspopup="true">
                   
                    <span class=" lg:flex lg:items-center">
                                           <!-- Chevron Down Icon -->
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"
                        class="transform transition-transform duration-200"
                        :class="{'rotate-180': open}">
                        <path d="M6 9L12 15L18 9" stroke="#1E1E1E" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    </span>
                </button>

                <!-- Dropdown menu -->
                <div x-show="open"
                    x-transition:enter="transition ease-out duration-100"
                    x-transition:enter-start="transform opacity-0 scale-95"
                    x-transition:enter-end="transform opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-75"
                    x-transition:leave-start="transform opacity-100 scale-100"
                    x-transition:leave-end="transform opacity-0 scale-95"
                    class="absolute min-w-fit right-8 top-12 z-10 mt-2.5 w-36 origin-top-right rounded-md bg-white py-2 shadow-lg ring-1 ring-gray-900/5 focus:outline-none max-[991px]:mt-[23px] max-[991px]:right-[0px] "
                    role="menu"
                    aria-orientation="vertical"
                    aria-labelledby="user-menu-button"
                    x-cloak>
                    <a href="{{ route('profile.change-password') }}" class="block px-3 py-1 text-[16px] font-[400] text-[#212529] hover:text-blue cursor-pointer whitespace-nowrap" role="menuitem" tabindex="-1">Change Password</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="cursor-pointer block w-full px-3 py-1 text-left text-[16] font-[400] text-[#212529] hover:text-blue" role="menuitem" tabindex="-1">{{ __('Logout') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>

<!-- Spacer to prevent content from going under fixed header -->

