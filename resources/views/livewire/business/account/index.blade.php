<div class="Account-setting-box">
    <x-loading target="changeTab" />
    <div class="profile-info-box">
        <div class="profile-user-box">
            <div class="preview-box">
                @if($logoUrl)
                <div class="size-48 rounded-full border-[1px] border-[#0D44EA] bg-[#EFF6FF] flex items-center justify-center">
                    <img x-ref="previewImage" class="preview-image" src="{{ $logoUrl }}" alt="{{ __('Business logo') }}">
               </div>
                @else
                    <div class="size-48 rounded-full border-[1px] border-[#0D44EA] bg-[#EFF6FF] flex items-center justify-center">
                        <span class="text-[50px] font-[500] text-[#0D44EA]">{{ $user->business->user_initials }}</span>
                    </div>
                @endif
                <x-loading target="logo" />
                <div for="imageInput" class="icon-box">
                    <input type="file" x-ref="imageInput" id="imageInput" wire:model="logo" class="imgage-input-box"/>
                    <x-icons name="camera" class="icon" />
                </div>
            </div>
             
        </div>
        <div class="profile-info-description">
            <h1 class="title text-[24px] font-[600] text-[#212529]">{{ $user->first_name }} {{ $user->last_name }}</h1>
            <p class="description text-[18px] font-[400] text-[#4B5563]"> {{ $user->email }}</p>
            @error('logo')<div class="error-message-box">{{ $message }}</div>@enderror
        </div>
    </div>
    <div class="flex flex-col w-full m-w-[100%] max-w-3/4 max-[1199px]:max-w-full">
    <div class="myaccount-tab-box">
        <div class="nav-tabs-box">
            @include('business.account.tabs.tab-nav')
        </div>

        @if($activeTab === 'profile')
        <livewire:business.profile.profile />
        @endif

        @if($activeTab === 'index')
            @include('business.account.tabs.pricing')
        @endif

        @if($activeTab === 'my-plan')
            @include('business.account.tabs.my-plan')
        @endif
    </div>
        <div class="tab-contact-us">
            <p class="font-[500] text-[16px] text-[#767676]">{{ __('Need some help?') }}</p>
            <a class="font-[600] text-[16px] text-blue hover:opacity-80" href="mailto:{{ $adminEmail }}">{{ __('Contact Us') }}</a>
        </div>
    </div>
</div>
