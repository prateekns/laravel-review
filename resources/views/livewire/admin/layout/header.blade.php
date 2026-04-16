<div class="bg-white sticky top-0 z-10 flex h-16 shrink-0 items-center gap-x-4 px-4 sm:gap-x-6 sm:px-6 lg:px-8 border-b border-gray-200">
    <!-- Mobile menu button -->
    <button type="button"
            wire:click="toggleMobileMenu"
            class="-m-2.5 p-2.5 text-gray-700 lg:hidden hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all duration-200 rounded-md">
        <span class="sr-only">{{ __('Open sidebar') }}</span>
        <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
        </svg>
    </button>

    <div class="flex flex-1 gap-x-4 self-stretch lg:gap-x-6 justify-end w-full px-4 sm:px-6 lg:px-4">
        <div class="flex items-center gap-x-4 lg:gap-x-6">

            <!-- Profile dropdown -->
            <div class="relative"
                 x-data="{ open: false }"
                 @click.outside="open = false"
                 @keydown.escape.window="open = false">
                
                <button @click="open = !open"
                        type="button"
                        class="flex items-center cursor-pointer bg-white border border-gray-200 rounded-md hover:bg-gray-50 focus:outline-none transition-all duration-200"
                        :aria-expanded="open"
                        aria-haspopup="true">
                    <span class="sr-only">{{ __('Open user menu') }}</span>
                    
                    @if($user)
                        @if($user->avatar)
                        <img class="size-8 rounded bg-gray-50 object-cover"
                             src="{{ $user->user_avatar }}"
                             alt="{{ $user->name }}"
                             />
                        @else
                        <div class="size-8 rounded bg-yellow-500 flex items-center justify-center">
                            <span class="text-sm/6 font-semibold text-gray-900">{{ $user->user_initials }}</span>
                        </div>
                        @endif
                        
                        <span class="hidden lg:flex lg:items-center">
                            <span class="ml-4 text-sm/6 font-semibold text-gray-900" aria-hidden="true">
                                {{ $user->name }}
                            </span>
                            <svg class="ml-2 size-5 text-gray-400 transition-transform duration-200"
                                 :class="{ 'rotate-180': open }"
                                 viewBox="0 0 20 20"
                                 fill="currentColor"
                                 aria-hidden="true">
                                <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                            </svg>
                        </span>
                    @endif
                </button>

                <!-- Dropdown menu -->
                <div x-show="open"
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="transform opacity-0 scale-95"
                     x-transition:enter-end="transform opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="transform opacity-100 scale-100"
                     x-transition:leave-end="transform opacity-0 scale-95"
                     class="absolute right-0 z-10 mt-2.5 w-56 origin-top-right rounded-md bg-white py-2 shadow-lg ring-1 ring-gray-900/5 focus:outline-none"
                     role="menu"
                     aria-orientation="vertical"
                     x-cloak>
                    
                    <div class="py-1">
                        <a href="{{ route('admin.account') }}"
                           class="group flex items-center px-4 py-2 text-sm text-gray-700"
                           role="menuitem">
                            <svg class="mr-3 size-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            </svg>
                            {{ __('admin.header.my_account') }}
                        </a>
                    </div>
                    
                    <div class="border-t border-gray-100 py-1">
                        <button wire:click="logout"
                                wire:loading.attr="disabled"
                                class="group flex w-full items-center px-4 py-2 text-sm text-gray-700 cursor-pointer"
                                role="menuitem">
                            <svg class="mr-3 size-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
                            </svg>
                            <span wire:target="logout">{{ __('admin.header.sign_out') }}</span>
                            <span wire:loading wire:target="logout" class="ml-2 animate-spin rounded-full h-4 w-4 border-b-3 border-white-800"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
