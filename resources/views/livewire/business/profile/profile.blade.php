<div x-data="{
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
    <div x-show="showSuccess" x-cloak>
        <x-toast type="success" message="successMessage" x-show="successMessage"/>
    </div>
    <div x-show="showError" x-cloak>
        <x-toast type="error"  message="errorMessage" x-show="errorMessage"/>
    </div>
<form wire:submit="updateProfile">
    <div class="user-profile-details gap-[20px] flex-col flex p-[20px]">
            
                <!-- Personal Information Section -->
                <div class="flex flex-col w-full gap-3">
                    <h2 class="text-[16px] text-[#1C1D1D] font-[600]">Personal Information</h2>
                    <div class="w-full h-[1px] bg-pool-gray-200"></div>
                </div>

                <!-- Name Fields -->
                <div class="form-group grid grid-cols-2 max-[767px]:grid-cols-1 gap-[20px]">
                    <div class="flex flex-col gap-2 flex-1">
                        <x-form.label required>{{ __('First Name') }}</x-form.label>
                        
                        <div class="flex flex-col w-full gap-0.5">
                        <x-form.text
                                name="first_name"
                                placeholder="{{ __('Enter First Name') }}"
                                wire:model="first_name"
                            />
                        </div>
                        @error('first_name')<div class="error-message-box">{{ $message }}</div>@enderror
                    </div>
                    <div class="flex flex-col gap-2 flex-1">
                        <x-form.label required>{{ __('Last Name') }}</x-form.label>
                       
                        <div class="flex flex-col w-full gap-0.5">
                            <x-form.text
                                name="last_name"
                                placeholder="{{ __('Enter Last Name') }}"
                                wire:model="last_name"
                            />
                        </div>
                         @error('last_name')<div class="error-message-box">{{ $message }}</div>@enderror
                    </div>
                </div>

                <!-- Business Fields -->
                <div class="form-group grid grid-cols-2 max-[767px]:grid-cols-1 gap-[20px]">
                    <div class="flex flex-col gap-2 flex-1">
                        <x-form.label required>{{ __('Business Name') }}</x-form.label>
                       
                        <div class="flex flex-col w-full gap-0.5">
                            <x-form.text
                                name="business_name"
                                placeholder="{{ __('Enter Business Name') }}"
                                wire:model="business_name"
                            />
                        </div>
                         @error('business_name')<div class="error-message-box">{{ $message }}</div>@enderror
                    </div>
                    <div class="flex flex-col gap-2 flex-1">
                        <x-form.label required>{{ __('Business Email Address') }}</x-form.label>
                        
                        <div class="flex flex-col w-full gap-0.5">
                            <x-form.text
                                name="email"
                                placeholder="{{ __('Enter Email Address') }}"
                                wire:model="email"
                                readonly
                            />
                        </div>
                        @error('email')<div class="error-message-box">{{ $message }}</div>@enderror
                    </div>
                </div>

                <!-- Contact Fields -->
                <div class="form-group grid grid-cols-2 max-[767px]:grid-cols-1 gap-[20px]">
                    <div class="flex flex-col gap-2 flex-1">
                        <x-form.label required>{{ __('Phone Number') }}</x-form.label>
                      
                        <div class="flex items-center gap-2">
                            <div class="code-inputs">
                            <x-form.text
                                name="isd_code"
                                value="{{ old('isd_code', $isd_code) }}"
                                readonly
                            />
                            </div>
                            <x-form.input-number
                                name="phone"
                                placeholder="{{ __('Enter Phone Number') }}"
                                wire:model="phone"
                            />
                            
                        </div>
                          @error('phone')<div class="error-message-box">{{ $message }}</div>@enderror
                    </div>
                    <div class="flex flex-col gap-2 flex-1">
                        <x-form.label>{{ __('Website URL') }}</x-form.label>
                       
                        <div class="flex flex-col w-full gap-0.5">
                            <x-form.text
                                name="website_url"
                                placeholder="{{ __('Enter Website URL') }}"
                                wire:model="website_url"
                            />
                        </div>
                         @error('website_url')<div class="error-message-box">{{ $message }}</div>@enderror
                    </div>
                </div>

                <!-- Address Fields -->
                <div class="form-group grid grid-cols-2 max-[767px]:grid-cols-1 gap-[20px]">
                    <div class="flex flex-col gap-2 flex-1">
                        <x-form.label required>{{ __('Address') }}</x-form.label>
                        
                        <div class="flex flex-col w-full gap-0.5 mt-2">
                            <x-form.textarea
                                name="address"
                                placeholder="{{ __('Enter Address') }}"
                                wire:model="address"
                                class="text-box"
                                rows="2"
                            />
                        </div>
                        @error('address')<div class="error-message-box">{{ $message }}</div>@enderror
                    </div>
                    <div class="flex flex-col gap-2 flex-1">
                        <x-form.label required>{{ __('Street') }}</x-form.label>
                        
                        <div class="flex flex-col w-full gap-0.5 mt-2">
                            <x-form.textarea
                                name="street"
                                placeholder="{{ __('Enter Street') }}"
                                wire:model="street"
                                class="text-box"
                                rows="2"
                            />
                        </div>
                        @error('street')<div class="error-message-box">{{ $message }}</div>@enderror
                    </div>
                </div>

                <!-- Location Fields -->
                <div class="grid grid-cols-12 max-[767px]:grid-cols-1 gap-6">
                    <div class="col-span-3">
                        <x-form.label required>{{ __('Zip Code') }}</x-form.label>
                       
                        <div class="flex flex-col w-full gap-0.5 mt-2">
                            <x-form.text
                                name="zipcode"
                                placeholder="{{ __('Enter Zip Code') }}"
                                wire:model="zipcode"
                            />
                        </div>
                         @error('zipcode')<div class="error-message-box">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-span-3">
                        <x-form.label required>{{ __('City') }}</x-form.label>
                       
                        <div class="flex flex-col w-full gap-0.5 mt-2">
                            <x-form.select
                                name="city"
                                placeholder="Select City"
                                :options="$cities"
                                :selected="$user->business->city_id"
                                wireModel="cityId"
                            />
                        </div>
                         @error('cityId')<div class="error-message-box">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-span-3">
                        <x-form.label required>{{ __('State') }}</x-form.label>
                      
                        <div class="flex flex-col w-full gap-0.5 mt-2">
                            <x-form.select
                                name="state"
                                placeholder="Select State"
                                :options="$states"
                                :selected="$user->business->state_id"
                                wireChange="onStateChanged"
                                wireModel="stateId"
                            />
                        </div>
                          @error('stateId')<div class="error-message-box">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-span-3">
                        <x-form.label required>{{ __('Country') }}</x-form.label>
                       
                        <div class="flex flex-col w-full gap-0.5 mt-2">
                            <x-form.select
                                name="country"
                                placeholder="Select Country"
                                :options="$countries"
                                :selected="$user->business->country_id"
                                wireChange="onCountryChanged"
                                wireModel="countryId"
                            />
                        </div>
                         @error('countryId')<div class="error-message-box">{{ $message }}</div>@enderror
                    </div>
                </div>

            <!-- Action Buttons -->
            <div class="flex flex-row w-full gap-6 max-[767px]:flex-col">
                <x-form.button class="btn" wireTarget="updateProfile"> {{__('Update Profile')}} </x-form.button>
                <x-form.link class="outlined btn-box" :link="route('dashboard')">{{__('Cancel')}}</x-form.link>
            </div>
       
    </div>
</form>
</div>
