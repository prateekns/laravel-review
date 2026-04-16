<div class="container-fluid mx-auto px-4 py-2">
    <div class="w-full">
        <div class="flex justify-between items-center mb-6">
            <div class="flex items-center">
                <div>
                    <a href="{{ route('dashboard') }}" class="flex flex-col">
                        <p class="text-[16px] font-[400] text-[#0C1421] flex items-center gap-[8px]">
                            <x-icons name="back"/>
                            <span>{{ __('Back')}}</span>
                        </p>
                    </a>
                    <h1 class="text-[32px] font-[700] text-[#212529]">{{ __('Change Password')}}</h1>
                </div>
            </div>
        </div>

        @if (session()->has('error'))
            <x-notification-alert type="error" :message="session('error')" />
        @endif

        <form
            wire:submit="changePassword"
            x-data="{showCurrentPassword: false,showPassword: false,showConfirmPassword: false}">
            @csrf
            <div class="mb-4">
                <div class="white-box">
                    <div class="flex max-w-[50%] flex-col max-[767px]:max-w-[100%]">
                        <div class="mb-2 col-span-3">
                            <div class="flex items-center mb-2">
                                <x-form.label for="current_password">{{__('Current Password')}}</x-form.label>
                            </div>

                            <x-form.text
                                type="password"
                                name="current_password"
                                placeholder="{{ __('Enter Current Password') }}"
                                wire:model="current_password"
                                class="input-box"
                            />
                            @error('current_password')<p class="error-message-box">{{ $message }}</p>@enderror
                        </div>

                        <div class="mb-2 col-span-3">
                            <div class="flex items-center mb-2">
                                <x-form.label for="password">{{__('New Password')}}</x-form.label>
                            </div>

                            <x-form.text
                                type="password"
                                name="password"
                                placeholder="{{ __('Enter New Password') }}"
                                wire:model="password"
                            />
                            @error('password')<p class="error-message-box">{{ $message }}</p>@enderror
                        </div>

                        <div class="mb-2 col-span-3">
                            <div class="flex items-center mb-2">
                               <x-form.label for="confirm_password">{{__('Confirm New Password')}}</x-form.label>
                                
                            </div>
                            <x-form.text
                                type="password"
                                name="confirm_password"
                                placeholder="{{ __('Enter Confirm New Password') }}"
                                wire:model="confirm_password"
                            />
                            @error('confirm_password')<p class="error-message-box">{{ $message }}</p>@enderror
                        </div>
                    </div>
                    <div class="flex justify-start gap-[24px] mt-[24px]">
                <x-form.button type="submit" class="btn" wireTarget="changePassword"> {{__('Update Password')}} </x-form.button>
                <x-form.link class="btn-box outlined" :link="route('dashboard')">{{__('Cancel')}}</x-form.link>
            </div>
                </div>
            </div>

            
        </form>
    </div>
</div>
