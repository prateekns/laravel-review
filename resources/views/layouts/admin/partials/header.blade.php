<!-- Header partial will go here -->
<div class="bg-white sticky top-0 z-10 flex h-16 shrink-0 items-center gap-x-4 px-4 sm:gap-x-6 sm:px-6 lg:px-8">
    <button type="button" class="-m-2.5 p-2.5 text-gray-700 lg:hidden">
        <span class="sr-only">Open sidebar</span>
        <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" data-slot="icon">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
        </svg>
    </button>

    <!-- Separator -->
    <div class="flex flex-1 gap-x-4 self-stretch lg:gap-x-6 justify-end max-w-7xl px-4 sm:px-6 lg:px-4">
        <div class="flex items-center gap-x-4 lg:gap-x-6">

            <!-- Profile dropdown -->
            <div x-data="{ open: false }" @click.away="open = false" @keydown.escape.window="open = false" class="relative">
                <button @click="open = !open"
                        type="button"
                        class="flex items-center cursor-pointer bg-white border border-gray-200 rounded-md"
                        id="user-menu-button"
                        :aria-expanded="open"
                        aria-haspopup="true">
                    <span class="sr-only">Open user menu</span>
                    <img class="size-8 rounded bg-gray-50" src="{{ Auth::user()->user_avatar }}" alt="{{ Auth::user()->name }}">
                    <span class="hidden lg:flex lg:items-center">
                        <span class="ml-4 text-sm/6 font-semibold text-gray-900" aria-hidden="true">{{ Auth::user()->name }}</span>
                        <svg class="ml-2 size-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon">
                            <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
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
                    class="absolute right-0 z-10 mt-2.5 w-32 origin-top-right rounded-md bg-white py-2 shadow-lg ring-1 ring-gray-900/5 focus:outline-none"
                    role="menu"
                    aria-orientation="vertical"
                    aria-labelledby="user-menu-button"
                    x-cloak>
                    <a href="{{ route('admin.account') }}" class="block px-3 py-1 text-sm/6 text-gray-900 hover:bg-gray-50" role="menuitem" tabindex="-1">{{ __('My Account') }}</a>
                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <button type="submit" class="block w-full px-3 py-1 text-left text-sm/6 text-gray-900 hover:bg-gray-50 cursor-pointer" role="menuitem" tabindex="-1">{{ __('Sign Out') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Alpine.js styles for x-cloak -->
<style>
    [x-cloak] { display: none !important; }
</style>
