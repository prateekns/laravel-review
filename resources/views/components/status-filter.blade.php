@props(['statusFilter' => ''])
<div class="relative inline-block text-left" x-data="{ open: false }" @click.away="open = false" x-cloak>
    <div>
        <button type="button"
                @click="open = !open"
                class="inline-flex w-full justify-center gap-x-1.5 rounded-md bg-white px-3 py-2.5 text-sm text-base text-gray-900 shadow-xs ring-1 ring-gray-300 ring-inset hover:bg-gray-50 sm:text-sm/6"
                :aria-expanded="open"
                aria-haspopup="true">
            {{ $statusFilter ? __(ucfirst($statusFilter)) : __('admin.all_status') }}
            <svg class="-mr-1 size-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon">
                <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
            </svg>
        </button>
    </div>

    <div x-show="open"
            x-transition:enter="transition ease-out duration-100"
            x-transition:enter-start="transform opacity-0 scale-95"
            x-transition:enter-end="transform opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-75"
            x-transition:leave-start="transform opacity-100 scale-100"
            x-transition:leave-end="transform opacity-0 scale-95"
            class="absolute right-0 z-10 mt-2 w-56 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black/5 focus:outline-hidden"
            role="menu"
            aria-orientation="vertical"
            aria-labelledby="menu-button"
            tabindex="-1">
        <div class="py-1" role="none">
            <button type="button"
                    wire:click="$set('statusFilter', '')"
                    @click="open = false"
                    class="block w-full px-4 py-2 text-left text-sm {{ !$statusFilter ? 'bg-gray-100 text-gray-900' : 'text-gray-700' }} hover:bg-gray-100"
                    role="menuitem">
                {{ __('admin.all_status') }}
            </button>
            <button type="button"
                    wire:click="$set('statusFilter', 'active')"
                    @click="open = false"
                    class="block w-full px-4 py-2 text-left text-sm {{ $statusFilter === 'active' ? 'bg-gray-100 text-gray-900' : 'text-gray-700' }} hover:bg-gray-100"
                    role="menuitem">
                {{ __('admin.active') }}
            </button>
            <button type="button"
                    wire:click="$set('statusFilter', 'inactive')"
                    @click="open = false"
                    class="block w-full px-4 py-2 text-left text-sm {{ $statusFilter === 'inactive' ? 'bg-gray-100 text-gray-900' : 'text-gray-700' }} hover:bg-gray-100"
                    role="menuitem">
                {{ __('admin.inactive') }}
            </button>
        </div>
    </div>
</div>
