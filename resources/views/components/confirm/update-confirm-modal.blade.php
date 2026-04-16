@props([
    'title' => '',
    'description' => '',
    'btnCancel' => __('admin.button.cancel'),
    'btnConfirm' => __('admin.button.confirm'),
])

<div x-show="showUpdateConfirm" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title"
    role="dialog" aria-modal="true">
    <!-- Background overlay -->
    <div class="fixed inset-0 bg-gray-900 opacity-50 transition-opacity"></div>

    <!-- Modal panel -->
    <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
        <div
            class="relative transform overflow-hidden rounded-lg bg-white px-4 pb-4 pt-5 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-6">
            <div class="confirm-modal-content">
                <div class="confirm-modal-content-box">
                    <div class="icon">
                        <svg width="74" height="75" viewBox="0 0 74 75" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <rect y="0.5" width="74" height="74" rx="37" fill="#EFF6FF" />
                            <path
                                d="M36.9999 31.3342V37.5008M36.9999 43.6675H37.0154M34.3637 23.41L21.3058 45.2092C21.0365 45.6754 20.8941 46.204 20.8926 46.7424C20.8911 47.2808 21.0306 47.8102 21.2972 48.2779C21.5638 48.7456 21.9482 49.1354 22.4122 49.4084C22.8763 49.6815 23.4037 49.8283 23.942 49.8342H50.0579C50.5962 49.8283 51.1236 49.6815 51.5876 49.4084C52.0517 49.1354 52.4361 48.7456 52.7027 48.2779C52.9693 47.8102 53.1088 47.2808 53.1073 46.7424C53.1058 46.204 52.9633 45.6754 52.6941 45.2092L39.6362 23.41C39.3614 22.9569 38.9744 22.5823 38.5126 22.3223C38.0508 22.0624 37.5299 21.9258 36.9999 21.9258C36.47 21.9258 35.949 22.0624 35.4873 22.3223C35.0255 22.5823 34.6385 22.9569 34.3637 23.41Z"
                                stroke="#0D44EA" stroke-width="3.08333" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>

                    </div>

                    <h3 class="confirm-modal-title" id="modal-title">
                        {{ $title }}
                    </h3>
                    <p class="confirm-modal-description">
                        {{ $description }}
                    </p>

                    <div class="confirm-modal-btn-box">
                        <button type="button" @click="handleUpdateCancel()" class="btn-box btn-blue-outline"
                            id="modal-cancel-button">
                            {{ $btnCancel }}
                        </button>
                        <button @click="handleUpdateConfirm()" type="button" class="btn-box btn"
                            id="modal-confirm-button">
                            {{ $btnConfirm }}
                            <span wire:loading wire:target="updateStatus"
                                class="ml-2 animate-spin rounded-full h-4 w-4 border-b-3 border-white-800"></span>
                        </button>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
