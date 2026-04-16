<div
    x-data="{
        showConfirm: false,
        status: @entangle('status'),
    }"
    @close-confirm.window="showConfirm = false;"
    @close-cancel.window="showConfirm = false;status=true"
    @close.window="showConfirm = false;"
>
<form wire:submit="save" class="space-y-6">
    <div class="form-content">
        <!-- First Name -->
        <div class="form-content-box">
            <div class="flex items-center mb-[6px]">
                <x-form.label for="first_name" required>{{__('First Name')}}</x-form.label>
            </div>
            <x-form.text
                :placeholder=" __('Enter First Name')"
                wire:model="first_name"
                name="first_name"
                id="first_name"
                type="text"
            />
            @error('first_name')
                <p class="error-message-box">{{ $message }}</p>
            @enderror
        </div>

        <!-- Last Name -->
        <div class="form-content-box">
            <div class="flex items-center mb-[6px]">
                <x-form.label for="last_name" required>{{__('Last Name')}}</x-form.label>
            </div>
            <x-form.text
                :placeholder=" __('Enter Last Name')"
                wire:model="last_name"
                name="last_name"
                id="last_name"
                type="text"
            />
            @error('last_name')
                <p class="error-message-box">{{ $message }}</p>
            @enderror
        </div>
   

    <!-- Email -->
    <div class="form-content-box">
        <div class="flex items-center mb-[6px]">
            <x-form.label for="email" required>{{__('Email Address')}}</x-form.label>
        </div>
        <x-form.text
            :placeholder=" __('Enter Email Address')"
            wire:model="email"
            name="email"
            id="email"
            type="text"
            :readonly="$subAdmin->id ? true : false"
            :disabled="$subAdmin->id ? true : false"
        />
        @error('email')
            <p class="error-message-box">{{ $message }}</p>
        @enderror
    </div>

    @if($subAdmin->id)
        <div class="w-12/12 block">
            <x-form.label for="last_name" required>{{__('admin.status')}}</x-form.label>
            <div class="flex items-center gap-[17px] mt-3">
                <div class="custom-radio-box">
                    <input type="radio" name="status" wire:model="status" value="1" class="mr-2" id="status_active">
                    <label for="status_active" class="mr-2 text-gray-500">{{ __('admin.active')}}</label>
                </div>
                <div class="custom-radio-box">
                    <input @change="showConfirm=true" type="radio" name="status" wire:model="status" value="0" class="mr-2" id="status_inactive">
                    <label for="status_inactive" class="mr-2 text-gray-500">{{ __('admin.inactive')}}</label>
                </div>
            </div>
        </div>
    @endif

    </div>

    <div class="form-content-btns">
        <x-form.button type="submit" class="btn" wireTarget="save">
            {{ $subAdmin->id ? __('Update') : __('Create Account') }}
        </x-form.button>

        <x-form.link :link="route('business.sub-admins.index')" class="btn-box outlined">
            {{ __('Cancel') }}
        </x-form.link>
    </div>
</form>
    <!-- Confirmation Modal -->
    @if($subAdmin->id)
        @include('components.confirm.confirm-modal' , [
            'title' => __('admin.alert.title_deactivate'),
            'description' => __('Are you sure you want to deactivate this sub-admin? This will immediately revoke their access.'),
        ])
    @endif

    @if(session()->has('success'))
        <x-notification-alert type="success" :message="session('success')" />
    @endif

    @if (session()->has('error'))
        <x-notification-alert type="error" :message="session('error')" />
    @endif
<div>

