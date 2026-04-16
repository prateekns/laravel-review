<div x-data = "{
        maxSize: @js($maxUploadSize),
        showToast: @entangle('showToast'),
        hideToast: function() {
                setTimeout(() => { this.showToast = false;}, 5000);
        },
        checkSize(event) {
            this.showToast = false;
            const file = event.target.files[0];
            const fileSizeInMB = file.size / (1024 * 1024);
            const fileSizeInMBRounded = fileSizeInMB.toFixed(2);
            if (file && fileSizeInMBRounded > this.maxSize) {
                event.target.value = '';
            }
            this.hideToast();
        },
    }"
    @hide-toast="hideToast()"
>

    @if (session()->has('success'))
        <x-notification-alert type="success" :message="session('success')" />
    @endif

    @if (session()->has('error'))
        <x-notification-alert type="error" :message="session('error')" />
    @endif

    @error('video')
        <div x-show="showToast" x-init="setTimeout(() => showToast = false, 5000)">
            <x-toast :message="$message" type="error"/>
        </div>
    @enderror

    <div class="divide-y divide-gray-200 overflow-hidden rounded-lg bg-white shadow-sm">
        <div class="px-4 py-5 sm:px-6 sm:flex sm:items-center">
            <div class="sm:flex-auto">
                <h1 class="text-base font-semibold text-gray-900">{{ __('admin.training_video.training_video') }}</h1>
            </div>
            <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
                @if($videoPath)
                    <button type="button" class="inline-flex items-center justify-center rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-blue-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 cursor-pointer" onclick="document.getElementById('file-upload').click()">{{ __('admin.training_video.upload_video') }}</button>
                @endif
            </div>
        </div>
        <div class="shadow-sm border-gray-200 px-4 pb-4 sm:px-6">
            <div
                class="mt-2 flex justify-center rounded-lg items-center min-h-96 {{ $videoPath ? 'mt-4' : 'border border-dashed border-gray-900/25 px-6 py-4' }}"
                tabindex="0"
                role="button"
                aria-label="{{ __('Upload Training Video') }}"
                @class(['border-red-500' => $errors->has('video')])
            >
                @if($videoPath)
                    <video class="w-full rounded-lg" controls src="{{ $videoPath }}" id="training-video">
                        <track kind="captions" src="" srclang="en" label="English" default>
                        {{ __('admin.training_video.browser_not_support') }}
                    </video>
                @endif

                <div class="text-center {{ $videoPath ? 'hidden' : '' }}">
                    <svg class="mx-auto size-12 text-gray-300" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" data-slot="icon">
                        <path fill-rule="evenodd" d="M1.5 6a2.25 2.25 0 0 1 2.25-2.25h16.5A2.25 2.25 0 0 1 22.5 6v12a2.25 2.25 0 0 1-2.25 2.25H3.75A2.25 2.25 0 0 1 1.5 18V6ZM3 16.06V18c0 .414.336.75.75.75h16.5A.75.75 0 0 0 21 18v-1.94l-2.69-2.689a1.5 1.5 0 0 0-2.12 0l-.88.879.97.97a.75.75 0 1 1-1.06 1.06l-5.16-5.159a1.5 1.5 0 0 0-2.12 0L3 16.061Zm10.125-7.81a1.125 1.125 0 1 1 2.25 0 1.125 1.125 0 0 1-2.25 0Z" clip-rule="evenodd"></path>
                    </svg>
                    <div class="mt-4 text-sm/6 text-gray-600">
                        <label for="file-upload" class="relative cursor-pointer rounded-md bg-white font-semibold text-indigo-600 focus-within:outline-hidden hover:text-indigo-500">
                            <span>{{ __('admin.training_video.upload_a_video') }}</span>
                            <input id="file-upload"
                                type="file"
                                class="sr-only"
                                accept=".avi,video/mp4,video/quicktime,video/x-msvideo,video/x-matroska"
                                wire:model="video"
                                wire:loading.attr="disabled"
                                @change="checkSize"
                            >
                        </label>
                    </div>
                    <p class="text-xs/5 text-gray-600">{{ __('admin.training_video.mp4_mov_mkv_up_to_30mb') }}</p>
                </div>

                <div wire:loading wire:target="video" class="absolute top-[50%] left-[50%]">
                    <div class="flex items-center justify-center">
                        <svg class="animate-spin h-8 w-8 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span class="ml-2 text-sm text-gray-600">{{ __('admin.training_video.uploading_video') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
