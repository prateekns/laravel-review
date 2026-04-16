<div x-data="{
    activeTab: 'account',
    confirmDelete: @entangle('confirmDelete').live,
    confirm: @entangle('confirm').live,
    error: @entangle('error'),
    init() {
        Livewire.on('openNewTab', ({ url }) => {
            window.open(url, '_blank');
        });
    }
}"
@cancelled="$wire.cancelAction()"
@confirm-delete="$wire.delete()"
@cancel="$wire.cancelAction()">

@if($business)
    <!-- Page header -->
    <div class="mx-auto  px-4 sm:px-6 md:flex md:items-center md:justify-between md:space-x-5 w-full lg:px-8">
    <div class="flex items-center space-x-5">
        <div class="shrink-0">
        <div class="relative">
            @if($business->business_logo)
                <img class="size-16 rounded-full object-contain" src="{{ $business->business_logo }}" alt="">
            @else
                <div class="size-16 rounded-full bg-yellow-500 flex items-center justify-center">
                    <span class="text-xl font-semibold text-gray-800">{{ $business->user_initials }}</span>
                </div>
            @endif
            <span class="absolute inset-0 rounded-full shadow-inner" aria-hidden="true"></span>
        </div>
        </div>
        <div>
        <h1 class="text-2xl font-bold text-gray-900">{{ $business->name }}</h1>
        </div>
    </div>
    <div class="mt-6 flex flex-col-reverse justify-stretch space-y-4 space-y-reverse sm:flex-row-reverse sm:justify-end sm:space-y-0 sm:space-x-3 sm:space-x-reverse md:mt-0 md:flex-row md:space-x-3">
    <a href="{{ route('admin.business.index') }}">
        <button type="button" class="inline-flex items-center justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-xs ring-1 ring-gray-300 ring-inset hover:bg-gray-50 cursor-pointer">{{ __('admin.button.back') }}</button>
    </a>
    
    
        <button type="button" @click="$wire.showDeleteConfirm({{$business->id}})" class="inline-flex items-center justify-center rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-blue-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 cursor-pointer">
            {{ __('admin.button.delete_business') }}
            <span wire:loading wire:target="delete" class="ml-2 animate-spin rounded-full h-4 w-4 border-b-3 border-white-800"></span>
        </button>

        <button type="button" @click="$wire.showLoginConfirm()" class="inline-flex items-center justify-center rounded-md bg-green-600 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-green-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-600 cursor-pointer">
            {{ __('admin.button.login_as') }}
            <span wire:loading wire:target="loginAsBusiness" class="ml-2 animate-spin rounded-full h-4 w-4 border-b-3 border-white-800"></span>
        </button>
    </div>
    </div>

    <div class="mt-8">
        <div class="hidden sm:block">
            <nav class="isolate flex divide-x divide-gray-200 rounded-lg shadow-sm" aria-label="Tabs">
                <x-form.tab-button name="account" active-tab="{{$activeTab}}">
                    {{ __('admin.business.business_admin') }}
                </x-form.tab-button>
                <x-form.tab-button name="technicians" active-tab="{{$activeTab}}">
                    {{ __('admin.business.technicians') }}
                </x-form.tab-button>
                    <x-form.tab-button name="billing" active-tab="{{$activeTab}}">
                    {{ __('admin.business.billing_history') }}
                </x-form.tab-button>
                <x-form.tab-button name="sub-admin" active-tab="{{$activeTab}}">
                    {{ __('admin.business.sub_admins') }}
                </x-form.tab-button>
                <x-form.tab-button name="customers" active-tab="{{$activeTab}}">
                    {{ __('admin.business.customers') }}
                </x-form.tab-button>
            </nav>
        </div>

        <!-- Tab Content -->
        <div class="mt-8">
            <x-loading :target="'switchTab'"/>

            <!-- Account Tab -->
            <div x-show="activeTab === 'account'" x-cloak>
                <livewire:admin.business.tabs.account :business="$business" wire:loading.remove wire:target="switchTab"/>
            </div>

            <!-- Technicians Tab -->
            <div x-show="activeTab === 'technicians'" x-cloak>
                <livewire:admin.business.tabs.technician
                    :business="$business"  wire:loading.remove wire:target="switchTab" :key="'technician-'.now()->timestamp"/>
            </div>

            <!-- Billing Tab -->
            <div x-show="activeTab === 'billing'" x-cloak>
                <livewire:admin.business.tabs.billing
                    :business="$business"  wire:loading.remove wire:target="switchTab" :key="'billing-'.now()->timestamp"/>
            </div>

            <!-- Sub Admins Tab -->
            <div x-show="activeTab === 'sub-admin'" x-cloak>
                <livewire:admin.business.tabs.sub-admin
                    :business="$business"  wire:loading.remove wire:target="switchTab" :key="'sub-admin-'.now()->timestamp"/>
            </div>

            <!-- Customers Tab -->
            <div x-show="activeTab === 'customers'" x-cloak>
                <livewire:admin.business.tabs.customer
                    :business="$business"  wire:loading.remove wire:target="switchTab" :key="'customers-'.now()->timestamp"/>
            </div>
    </div>
    </div>

    <!-- Business Delete Confirmation Modal -->
    <x-admin.confirm-delete
        message="{{ __('admin.alert.delete_business') }}"
        btnConfirm="{{ __('admin.button.delete') }}"
    />

    <!-- Business Delete Confirmation Modal -->
    <x-confirm.confirm
        message="{{ __('admin.alert.login_as', ['business' => $business->name]) }}"
        btnConfirm="{{ __('admin.button.confirm') }}"
        on-click="loginAs"
    />

    @if (session()->has('success'))
        <x-notification-alert type="success" :message="session('success')" />
    @endif

    @if (session()->has('error'))
        <x-notification-alert type="error" :message="session('error')"/>
    @endif

    <div x-show="error" x-cloak>
        <x-notification-alert type="error" :message="$error"/>
    </div>

@else
    <x-notification-alert type="error" :message="__('admin.message.business_not_loaded')" />
@endif
</div>
