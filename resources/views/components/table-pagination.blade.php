<div class="flex-1 flex justify-between sm:hidden">
    <button wire:click="previousPage" @disabled(!$list->previousPageUrl()) class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 enabled:cursor-pointer">
        {{ __('Previous') }}
    </button>
    <button wire:click="nextPage" @disabled(!$list->nextPageUrl()) class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 enabled:cursor-pointer">
        {{ __('Next') }}
    </button>
</div>

<div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
    <div>
        <p class="text-sm text-gray-700">
            {{ __('Showing') }}
            <span class="font-medium">{{ $list->firstItem() }}</span>
            {{ __('to') }}
            <span class="font-medium">{{ $list->lastItem() }}</span>
            {{ __('of') }}
            <span class="font-medium">{{ $list->total() }}</span>
            {{ __('results') }}
        </p>
    </div>
    <div class="flex items-center space-x-2">
        <button wire:click="previousPage" @disabled(!$list->previousPageUrl()) class="prev-btn relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 enabled:cursor-pointer">
            {{ __('Previous') }}
        </button>
        <button wire:click="nextPage" @disabled(!$list->nextPageUrl()) class="next-btn relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 enabled:cursor-pointer">
            {{ __('Next') }}
        </button>
    </div>
</div>
