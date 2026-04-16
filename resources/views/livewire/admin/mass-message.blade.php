<div>
    @if (session()->has('success'))
        <x-notification-alert type="success" :message="session('success')" />
    @endif

    @if (session()->has('error'))
        <x-notification-alert type="error" :message="session('error')" />
    @endif

   <form wire:submit="sendMessage">
        <div class="mb-4">
            <div class="grid grid-cols-4 gap-4">
                <div class="mb-2 col-span-4">
                    <div class="flex items-center mb-2">
                        <label for="first_name" class="block text-sm font-medium text-gray-700">{{ __('Message') }}<span class="text-red-600">*</span></label>
                        @error('message') <span class="ml-2 text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <textarea name="message" rows="4" wire:model="message" class="block w-1/2 rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-1 focus:-outline-offset-1 focus:outline-indigo-600 sm:text-sm/6 {{ $errors->has('message') ? 'outline-red-500' : '' }}"></textarea>
                </div>

                <div class="mb-2 col-span-2">
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">{{ __('admin.status')}}<span class="text-red-600">*</span></label>
                    <input type="radio" name="status" wire:model="status" id="status_active" value="1" class="mr-2" checked>
                    <label for="status_active" class="mr-2 text-gray-500">{{ __('admin.active')}}</label>

                    <input type="radio" name="status" id="status_inactive" wire:model="status" value="0" class="mr-2">
                    <label for="status_inactive" class="mr-2 text-gray-500">{{ __('admin.inactive')}}</label>
                </div>
            </div>
        </div>

        <div class="flex justify-start">
            <button type="submit" class="cursor-pointer bg-indigo-500 hover:bg-indigo-600 text-white px-4 py-2 rounded-lg mr-2">
                {{ $isUpdate ? __('admin.button.update_and_resend') : __('admin.button.send') }}
                <span wire:loading wire:target="sendMessage" class="ml-2 animate-spin rounded-full h-4 w-4 border-b-3 border-white-800"></span>
            </button>
        </div>
    </form>
<div>
