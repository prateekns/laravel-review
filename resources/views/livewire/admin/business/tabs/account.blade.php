<div
x-data="{
    showToast: false,
    showSuccess: false,
    showError: false,
    successMessage: '',
    errorMessage: '',
}"
@update-success.window="
        successMessage = $event.detail[0].message;
        showSuccess = true;
        showToast = true;
        setTimeout(() => {
            showToast = false;
            showSuccess = false;
        }, 5000);
    "
    @update-error.window="
        errorMessage = $event.detail[0].message;
        showError = true;
        showToast = true;
        setTimeout(() => {
            showToast = false;
            showError = false;
        }, 5000);
    ">
<div class="bg-white shadow-sm sm:rounded-lg border-t border-gray-200 px-4 py-5 sm:px-6">
    @if($business)
        <dl class="grid grid-cols-1 gap-x-4 gap-y-8 sm:grid-cols-3">
            <div class="sm:col-span-1">
                <dt class="text-sm font-medium text-gray-500">{{ __('admin.business.business_name') }}</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $business->name ?? __('admin.not_provided') }}</dd>
            </div>

            <div class="sm:col-span-1">
                <dt class="text-sm font-medium text-gray-500">{{ __('admin.business.business_admin_name') }}</dt>
                <dd class="mt-1 text-sm text-gray-900">
                    {{ $business->primaryUser?->first_name  ? $business->primaryUser?->adminName : __('admin.not_provided') }}
                </dd>
            </div>

            <div class="sm:col-span-1">
                <dt class="text-sm font-medium text-gray-500">{{ __('admin.business.business_email') }}</dt>
                <dd class="mt-1 text-sm text-gray-900">
                    @if($business->email)
                        <a href="mailto:{{ $business->email }}" class="hover:text-blue-400">{{ $business->email }}</a>
                    @else
                        {{__('admin.not_provided')}}
                    @endif
                </dd>
            </div>

            <div class="sm:col-span-1">
                <dt class="text-sm font-medium text-gray-500">{{ __('admin.business.business_phone') }}</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $business->phone ?? __('admin.not_provided') }}</dd>
            </div>

            <div class="sm:col-span-1">
                <dt class="text-sm font-medium text-gray-500">{{ __('admin.business.website') }}</dt>
                <dd class="mt-1 text-sm text-gray-900">
                    @if($business->website_url)
                        <a href="{{ $business->website_url }}" class="hover:text-blue-400" target="_blank">{{ $business->website_url }}</a>
                    @else
                        {{__('admin.not_provided')}}
                    @endif
                </dd>
            </div>

            <div class="sm:col-span-1">
                <dt class="text-sm font-medium text-gray-500">{{ __('admin.business.address') }}</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $business->address ?? __('admin.not_provided') }}</dd>
            </div>

            <div class="sm:col-span-1">
                <dt class="text-sm font-medium text-gray-500">{{ __('admin.business.country') }}</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $business->country?->name ?? __('admin.not_provided') }}</dd>
            </div>

            <div class="sm:col-span-1">
                <dt class="text-sm font-medium text-gray-500">{{ __('admin.business.state') }}</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $business->state?->name ?? __('admin.not_provided') }}</dd>
            </div>

            <div class="sm:col-span-1">
                <dt class="text-sm font-medium text-gray-500">{{ __('admin.business.city') }}</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $business->city?->name ?? __('admin.not_provided') }}</dd>
            </div>

            <div class="sm:col-span-1">
                <dt class="text-sm font-medium text-gray-500">{{ __('admin.business.street') }}</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $business->street ?? __('admin.not_provided') }}</dd>
            </div>

            <div class="sm:col-span-1">
                <dt class="text-sm font-medium text-gray-500">{{ __('admin.business.zip_code') }}</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $business->zipcode ?? __('admin.not_provided') }}</dd>
            </div>
        </dl>
    @else
      <div class="text-center">{{  __('admin.message.business_not_loaded') }}</div>
    @endif
</div>

@if($business && $is_trial_active && !$subscription)
    <div class="mt-4 bg-white shadow-sm sm:rounded-lg border-t border-gray-200 px-4 py-5 sm:px-6">
        <div class="overflow-hidden rounded-lg" >
                <div class="divide-y divide-white/5">
                    <div class="grid max-w-7xl grid-cols-1 gap-x-8 gap-y-4 px-4 py-5 sm:px-6 md:grid-cols-3 lg:px-8">
                        <div>
                            <h2 class="text-base/7 font-semibold text-gray-900">{{ __('admin.settings.trial_setting') }}</h2>
                            <p class="mt-1 text-sm/6 text-gray-400">{{ __('admin.settings.trial_period_update_description') }}</p>
                        </div>
                        @if($is_trial_active && !$subscription)
                            <form wire:submit="updateTrialEndAt" class="md:col-span-2">
                                @csrf
                                <div class="grid grid-cols-1 gap-x-6 gap-y-8 sm:max-w-xl sm:grid-cols-6">
                                    <div class="col-span-full">
                                        <div class="flex items-center justify-between">
                                            <label for="name" class="block text-sm/6 font-medium text-gray-900">{{ __('admin.settings.trial_end_date') }}</label>
                                            @error('trial_end_at')
                                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div class="mt-2">
                                            <input
                                                type="date"
                                                id="trial-days"
                                                wire:model="trial_end_at"
                                                value="{{ old('trial_end_at', $trial_end_date ) }}"
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
                             @elseif($subscription)
                           <div class="grid grid-cols-1 gap-x-6 gap-y-8 sm:max-w-xl sm:grid-cols-6">
                                <div class="col-span-full">
                                    <div class="flex items-center justify-between">
                                        <label for="name" class="block text-sm/6 font-medium text-gray-900">{{ __('admin.settings.trial_ended_at') }}</label>
                                    </div>
                                    <div class="mt-1 text-sm text-gray-900">
                                        {{ $business->trial_end_at->format('m/d/Y') }}
                                    </div>
                                </div>
                            </div>
                            @else
                            <div class="grid grid-cols-1 gap-x-6 gap-y-8 sm:max-w-xl sm:grid-cols-6">
                                <div class="col-span-full">
                                    <div class="flex items-center justify-between">
                                        <label for="name" class="block text-sm/6 font-medium text-gray-900">{{ __('admin.settings.trial_ended_at') }}</label>
                                    </div>
                                    <div class="mt-1 text-sm text-gray-900">
                                        {{ __('Not yet started.') }}
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
    </div>
@endif
    <div x-show="showSuccess" x-cloak>
        <x-toast type="success" message="successMessage" x-show="successMessage"/>
    </div>
    <div x-show="showError" x-cloak>
        <x-toast type="error"  message="errorMessage" x-show="errorMessage"/>
    </div>
</div>
