<div x-show="showConfirm" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <!-- Background overlay -->
    <div class="fixed inset-0 bg-gray-900 opacity-50 transition-opacity"></div>

    <!-- Modal panel -->
    <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
        <div class="relative transform overflow-hidden rounded-lg bg-white px-4 pb-4 pt-5 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-6">
            <div class="absolute right-0 top-0 hidden pr-4 pt-4 sm:block">
                <button type="button" @click="$dispatch('close-cancel');" class="cursor-pointer rounded-md bg-white text-gray-400 hover:text-gray-500 focus:outline-none">
                    <span class="sr-only">Close</span>
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="flex flex-col items-center justify-center">
                <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                    <h3 class="text-base font-semibold leading-6 text-gray-900" id="modal-title">
                        {{ $title }}
                    </h3>
                    <p class="mt-2 text-sm text-gray-500">
                        {{ $description }}
                    </p>
                    <div class="mt-6">
                        <div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                            <button type="button" @click="$dispatch('close-cancel');" class="cursor-pointer inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:w-auto" @click="cancelDeactivate">
                            {{ __('admin.button.cancel') }}
                            </button>
                            <button @click="$dispatch('close-confirm');" type="button" class="cursor-pointer inline-flex w-full justify-center rounded-md bg-indigo-500 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-400 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-500 sm:w-auto">
                                {{ __('admin.button.confirm') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
