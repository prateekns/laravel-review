<!-- Header partial will go here -->
<div class="sticky top-0 z-40 flex h-16 shrink-0 items-center gap-x-4 px-4 sm:gap-x-6 sm:px-6 lg:px-8">
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
                    @if(auth()->user()->business->business_logo)
                        <img class="size-8 rounded bg-gray-50" src="{{ Auth::user()->business->business_logo }}" alt="{{ Auth::user()->name }}">
                    @else
                        <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                            <span class="text-gray-600 text-lg font-medium">
                                {{ substr(Auth::user()->name ?? 'U', 0, 1) }}
                            </span>
                        </div>
                    @endif
                    <span class="hidden lg:flex lg:items-center">
                        <span class="ml-4 text-sm/6 font-semibold text-gray-900" aria-hidden="true">{{ Auth::user()->name }}</span>
                        <svg class="ml-2 size-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon">
                            <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                        </svg>
                    </span>
                </button>

                <!-- Dropdown menu -->
                <div x-show="open"
                    class="absolute right-0 z-10 mt-2.5 w-32 origin-top-right rounded-md bg-white py-2 shadow-lg ring-1 ring-gray-900/5 focus:outline-none"
                    role="menu"
                    aria-orientation="vertical"
                    aria-labelledby="user-menu-button"
                    x-cloak>
                    <form method="POST" action="{{ route('logout') }}">
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
