@extends('layouts.admin.app')

@section('title', __('admin.settings.title'))

@section('content')
<div class="container mx-auto"
x-data="{activeTab: 'price'}">
    <div class="max-w-7xl mx-auto">
        <div class="flex justify-between items-center">
            <div class="flex items-center">
                <div>
                    <a href="{{ route('admin.sub-admin') }}"  class="back-btn">
                        <p>
                            <x-icons name="back"/>
                            <span>{{__('Back')}}</span>
                        </p>
                    </a>
                    <h1 class="text-lg font-medium main-heading mt-4">
                        {{__('admin.settings.title')}}
                    </h1>
                    <p class="sub-heading">{{ __('Update the settings for the application.')}}</p>
                </div>
            </div>
        </div>

        <div class="hidden sm:block mt-8">
            <nav class="isolate flex divide-x divide-gray-200 rounded-lg shadow-sm" aria-label="Tabs">
                <x-form.tab-button name="trail" active-tab="trail">
                    {{ __('admin.settings.trial_setting') }}
                </x-form.tab-button>
                <x-form.tab-button name="price" active-tab="">
                    {{ __('admin.settings.pricing_setting') }}
                </x-form.tab-button>
            </nav>
        </div>

        <div class="white-box">
            <div class="overflow-hidden rounded-lg" >
                <div x-show="activeTab === 'trail'" x-cloak>
                    <div class="divide-y divide-white/5">
                        <div class="grid max-w-7xl grid-cols-1 gap-x-8 gap-y-10 px-4 py-16 sm:px-6 md:grid-cols-3 lg:px-8">
                            <div>
                                <h2 class="text-base/7 font-semibold text-gray-900">{{ __('admin.settings.trial_setting') }}</h2>
                                <p class="mt-1 text-sm/6 text-gray-400">{{ __('admin.settings.trial_setting_description') }}</p>
                            </div>

                            <form action="{{ route('admin.setting.store') }}" method="POST" class="md:col-span-2">
                                @csrf
                                <div class="grid grid-cols-1 gap-x-6 gap-y-8 sm:max-w-xl sm:grid-cols-6">
                                    <div class="col-span-full">
                                        <div class="flex items-center justify-between">
                                            <label for="name" class="block text-sm/6 font-medium text-gray-900">{{ __('admin.settings.trial_period') }}</label>
                                            @error('trial_period')
                                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div class="mt-2">
                                            <input
                                                type="text"
                                                id="trial-days"
                                                name="trial_period"
                                                value="{{ old('trial_period', $settings->trial_period ?? config('app.trial_period')) }}"
                                                class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6"
                                            >
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-8 flex">
                                    <button type="submit" class="rounded-md bg-indigo-500 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-400 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-500 cursor-pointer">
                                        {{ __('admin.settings.save_changes') }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div x-show="activeTab === 'price'" x-cloak>
                    <livewire:admin.setting.price-setting wire:loading.remove wire:target="switchTab"/>
                </div>
            </div>
        </div>
    </div>
    @if (session()->has('success'))
        <x-notification-alert  type="success" :message="session('success')" />
    @endif

    @if (session()->has('error'))
        <x-notification-alert type="error" :message="session('error')" />
    @endif
    </div>
@endsection
