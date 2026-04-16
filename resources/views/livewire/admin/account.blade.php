<div class="overflow-hidden rounded-lg bg-white shadow-sm" x-data="{ showSuccess: false }" x-on:profile-updated.window="setTimeout(() => window.location.reload(), 1000)">
    <!-- Settings forms -->
    <div class="divide-y divide-white/5">
        <!-- Personal Information Section -->
        <div class="grid max-w-7xl grid-cols-1 gap-x-8 gap-y-10 px-4 py-16 sm:px-6 md:grid-cols-3 lg:px-8">
            <div>
                <h2 class="text-base/7 font-semibold text-gray-900">{{ __('admin.account.personal_information') }}</h2>
                <p class="mt-1 text-sm/6 text-gray-400">{{ __('admin.account.update_personal_information') }}</p>
            </div>

            <form wire:submit="updateProfile" class="md:col-span-2">
                @csrf
                <div class="grid grid-cols-1 gap-x-6 gap-y-8 sm:max-w-xl sm:grid-cols-6">
                    {{-- <div class="col-span-full flex items-center gap-x-8">
                        <div class="relative">
                            @if($avatarPreview)
                            <img src="{{ $avatarPreview }}" alt="{{ $name }}" class="size-24 flex-none rounded-lg bg-gray-100 object-contain">
                            @else
                            <div class="size-24 flex-none rounded-lg bg-yellow-500 flex items-center justify-center">
                                <span class="text-6xl text-white">{{ $user->user_initials }}</span>
                            </div>
                            @endif
                            <div wire:loading wire:target="avatar" class="absolute inset-0 flex items-center justify-center bg-gray-800/50 rounded-lg">
                                <svg class="animate-spin size-8 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </div>
                        </div>
                        <div>
                            <label class="rounded-md bg-indigo-500 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-400 cursor-pointer">
                                <input type="file" wire:model="avatar" class="hidden" accept="image/jpeg,image/png">
                                {{ __('admin.account.change_avatar') }}
                            </label>
                            <p class="mt-2 text-xs/5 text-gray-400">{{ __('admin.account.avatar_info') }}</p>
                            @error('avatar')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div> --}}

                    <div class="col-span-full">
                        <div class="flex items-center justify-between">
                            <label for="name" class="block text-sm/6 font-medium text-gray-900">{{ __('admin.label.name') }}</label>
                            @error('name')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mt-2">
                            <input
                                type="text"
                                id="name"
                                wire:model="name"
                                class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6"
                            >
                        </div>
                    </div>
                </div>

                <div class="mt-8 flex">
                    <button type="submit" class="rounded-md bg-indigo-500 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-400 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-500 cursor-pointer">
                        {{ __('Submit') }}
                    </button>
                </div>
            </form>
        </div>

        <!-- Change Password Section -->
        <div class="grid max-w-7xl grid-cols-1 gap-x-8 gap-y-10 px-4 py-16 sm:px-6 md:grid-cols-3 lg:px-8">
            <div>
                <h2 class="text-base/7 font-semibold text-gray-900">{{ __('admin.account.change_password') }}</h2>
                <p class="mt-1 text-sm/6 text-gray-400">{{ __('admin.account.change_password_info') }}</p>
            </div>

            <form wire:submit="updatePassword" class="md:col-span-2">
                @csrf
                <div class="grid grid-cols-1 gap-x-6 gap-y-6 sm:max-w-xl sm:grid-cols-6">
                   
                    <div class="col-span-full">
                        <div class="flex items-center justify-between">
                            <label for="current-password" class="block text-sm/6 font-medium text-gray-900">{{ __('admin.label.current_password') }}</label>
                           
                        </div>
                        <div class="mt-2">
                            <input
                                id="current-password"
                                type="password"
                                wire:model="current_password"
                                class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6"
                            />
                        </div>
                         @error('current_password')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                    </div>

                    <div class="col-span-full">
                        <div class="flex items-center justify-between">
                            <label for="new-password" class="block text-sm/6 font-medium text-gray-900">{{ __('admin.label.new_password') }}</label>
                            
                        </div>
                        <div class="mt-2">
                            <input
                                id="new-password"
                                type="password"
                                wire:model="new_password"
                                class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6"
                            >
                        </div>
                         @error('new_password')
                        @if(str_contains($message, 'uppercase'))
                        <p class="mt-2 text-sm text-red-600 col-span-full">{{ $message }}</p>
                        @endif
                    @enderror
                            @error('new_password')
                                @if(!str_contains($message, 'uppercase'))
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @endif
                            @enderror
                    </div>

                    <div class="col-span-full">
                        <div class="flex items-center justify-between">
                            <label for="confirm-password" class="block text-sm/6 font-medium text-gray-900">{{ __('admin.label.confirm_password') }}</label>
                            
                        </div>
                        <div class="mt-2">
                            <input
                                id="confirm-password"
                                type="password"
                                wire:model="confirm_password"
                                class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6"
                            >
                            
                        </div>
                        @error('confirm_password')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                    </div>
                </div>

                <div class="mt-4 flex">
                    <button type="submit" class="rounded-md bg-indigo-500 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-400 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-500 cursor-pointer">
                        {{ __('Submit') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    @if (session()->has('success'))
        <x-notification-alert  type="success" :message="session('success')" />
    @endif

    @if (session()->has('error'))
       <x-notification-alert type="error" :message="session('error')" />
    @endif
</div>
