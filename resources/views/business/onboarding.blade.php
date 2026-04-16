@extends('layouts.business.onboarding')
@section('title', 'Business Onboarding')
@section('content')

<div class="mt-10 sm:mx-auto max-[600px]:px-[20px]" x-data="onBoardingFormHandler()" x-init="init()">
    <div class="onboarding-header-box max-[767px]:!rounded-bl-none max-[767px]:!rounded-br-none">
        @if (session('error'))
        <x-alert type="error" :message="session('error')" />
        @endif
        @if (session('status'))
        <x-alert type="success" :message="session('status')" />
        @endif

        @if ($errors->any())
        <x-alert type="error" :message="$errors->first()" />
        @endif
        <div class="logo-box">
            <img class="form-logo" src="{{ asset('images/PoolRoute-logo-solid.svg') }}" alt="{{ config('app.name') }} Logo">
        </div>
        <div class="message-box">
            <div class="icon">
                <svg width="18" height="17" viewBox="0 0 18 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <g clip-path="url(#clip0_1340_12393)">
                        <path d="M6.07541 8.49991L8.19662 10.6211L12.439 6.37869M16.3279 8.49991C16.3279 12.405 13.1623 15.5706 9.25723 15.5706C5.35219 15.5706 2.18652 12.405 2.18652 8.49991C2.18652 4.59486 5.35219 1.4292 9.25723 1.4292C13.1623 1.4292 16.3279 4.59486 16.3279 8.49991Z" stroke="#16A34A" stroke-width="1.69697" stroke-linecap="round" stroke-linejoin="round" />
                    </g>
                    <defs>
                        <clipPath id="clip0_1340_12393">
                            <rect width="16.9697" height="16.9697" fill="white" transform="translate(0.772461 0.0151367)" />
                        </clipPath>
                    </defs>
                </svg>
            </div>
            <div class="message">
                <p class="text-[15px] font-[500] text-[#16A34A] m-[0]">{{ __('You are successfully logged In') }}</p>
            </div>

        </div>
        <div class="onboard-head-title-box">
            <p>{{ __('Please complete your details to personalize your experience') }}</p>
        </div>
        <div class="onboard-stepper">
            <nav class="onboard-stepper" aria-label="Progress">
                <ol role="list" class="flex items-center gap-[24px]">

                    <!-- Step 1 -->
                    <li class="step-item" :class="{ 'active': currentStep === 'businessDetails' }">
                        <div class="step-circle" x-show="currentStep == 'businessDetails'" x-cloak>1</div>
                        <div class="step-circle !bg-[#16A34A]" x-show="currentStep == 'businessAddress'" x-cloak>
                            <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M3.12229 11.116C3.12229 10.9947 3.09896 10.934 3.05229 10.934L2.73029 11.088C2.73029 11.0227 2.69296 10.976 2.61829 10.948L2.50629 10.934C2.43162 10.934 2.33829 10.9667 2.22629 11.032C2.20762 10.9853 2.18429 10.9387 2.15629 10.892C2.12829 10.8453 2.10496 10.8033 2.08629 10.766C1.96496 10.5327 1.84362 10.276 1.72229 9.996C1.61029 9.70667 1.50296 9.43133 1.40029 9.17C1.30696 8.90867 1.23229 8.70333 1.17629 8.554C1.13896 8.43267 1.09696 8.25067 1.05029 8.008C1.00362 7.76533 0.956956 7.45733 0.910289 7.084C1.01296 7.14933 1.09229 7.182 1.14829 7.182C1.21362 7.182 1.27429 7.084 1.33029 6.888C1.35829 6.92533 1.40962 6.944 1.48429 6.944C1.54029 6.944 1.58229 6.92533 1.61029 6.888L1.83429 6.552L2.08629 6.636H2.10029C2.11896 6.636 2.13762 6.62667 2.15629 6.608C2.17496 6.58933 2.20296 6.57067 2.24029 6.552C2.31496 6.50533 2.37096 6.482 2.40829 6.482L2.45029 6.496C2.68362 6.608 2.83296 6.81333 2.89829 7.112C3.06629 7.82133 3.23429 8.176 3.40229 8.176C3.57029 8.176 3.76629 7.99867 3.99029 7.644C4.10229 7.46667 4.21429 7.26133 4.32629 7.028C4.44762 6.79467 4.56896 6.53333 4.69029 6.244C4.70896 6.356 4.72762 6.412 4.74629 6.412C4.79296 6.412 4.87229 6.29533 4.98429 6.062C5.10562 5.82867 5.29696 5.50667 5.55829 5.096C5.70762 4.844 5.89429 4.55933 6.11829 4.242C6.35162 3.92467 6.59896 3.598 6.86029 3.262C7.12162 2.926 7.37362 2.60867 7.61629 2.31C7.86829 2.01133 8.09229 1.75467 8.28829 1.54C8.48429 1.32533 8.62896 1.18533 8.72229 1.12C9.07696 0.877333 9.35696 0.643999 9.56229 0.42C9.55296 0.485333 9.53896 0.545999 9.52029 0.602C9.51096 0.648666 9.50629 0.681333 9.50629 0.699999C9.50629 0.737333 9.52496 0.755999 9.56229 0.755999L9.95429 0.559999V0.616C9.95429 0.690666 9.97296 0.728 10.0103 0.728C10.0383 0.728 10.0943 0.686 10.1783 0.602C10.2623 0.518 10.309 0.457333 10.3183 0.42L10.2903 0.616L10.7663 0.336L10.6543 0.588C10.8036 0.485333 10.911 0.433999 10.9763 0.433999C11.0136 0.433999 11.0416 0.457333 11.0603 0.503999C11.079 0.541333 11.0883 0.578666 11.0883 0.616C11.0883 0.671999 11.065 0.737333 11.0183 0.812C10.9716 0.886666 10.911 0.975333 10.8363 1.078C10.7803 1.15267 10.687 1.26467 10.5563 1.414C10.435 1.554 10.2483 1.764 9.99629 2.044C9.74429 2.31467 9.40829 2.69267 8.98829 3.178C8.87629 3.29933 8.70362 3.514 8.47029 3.822C8.23696 4.12067 7.97096 4.47067 7.67229 4.872C7.38296 5.264 7.09362 5.66067 6.80429 6.062C6.51496 6.46333 6.25829 6.82733 6.03429 7.154C5.81029 7.47133 5.65162 7.70933 5.55829 7.868L4.69029 9.338C4.50362 9.65533 4.34962 9.91667 4.22829 10.122C4.10696 10.318 4.01362 10.4533 3.94829 10.528C3.80829 10.696 3.65429 10.8453 3.48629 10.976L3.36029 10.906L3.24829 10.976L3.12229 11.116Z" fill="white" />
                            </svg>
                        </div>
                        <span class="step-label">{{ __('Personal & Business Details') }}</span>
                    </li>

                    <!-- Divider -->
                    <li class="step-divider"></li>

                    <!-- Step 2 -->
                    <li class="step-item" :class="{ 'active': currentStep === 'businessAddress' }">
                        <div class="step-circle">2</div>
                        <span class="step-label">{{ __('Business Address') }}</span>
                    </li>

                </ol>
            </nav>
        </div>
    </div>

    <div class="onboarding-body">
        <form method="POST" action="{{ route('onboarding.store') }}" @submit.prevent="handleSubmit" x-ref="form" enctype="multipart/form-data">
            @csrf
            <div class="onbordstep-content-box">
                <div class="onboard-steps-box max-[600px]:!px-[0]" id="businessDetails" x-show="currentStep === 'businessDetails'" x-cloak>

                    <div class="onboard-steps-box-cards max-[767px]:!rounded-tl-none max-[767px]:!rounded-tr-none max-[767px]:!rounded-bl-none max-[767px]:!rounded-br-none">
                        <div class="onboard-steps-box-cards-title mb-[24px]">
                            <img src="{{ asset('images/user-icon.svg') }}" alt="" class="icon">
                            <h2>{{ __('Personal Details') }}</h2>
                        </div>
                        <div class="form-box">
                            <div class="w-full">
                                <x-form.label for="first_name" required>{{__('First Name')}}</x-form.label>


                                <div class="mt-1">
                                    <x-form.text
                                        name="first_name"
                                        placeholder="{{ __('Enter First Name')}}"
                                        x-ref="first_name"
                                        x-model="first_name"
                                        x-bind:class="{'outline-red-500': errors.first_name}"
                                        value="{{ old('first_name', $business->primaryUser?->first_name) }}" />
                                </div>
                                <div x-show="errors.first_name" x-text="errors.first_name" class="error-message-box"></div>
                                @error('first_name')<div class="error-message-box">{{ $message }}</div>@enderror
                            </div>

                            <div class="w-full">
                                <x-form.label for="last_name" required>{{__('Last Name')}}</x-form.label>

                                <div class="mt-1">
                                    <x-form.text
                                        name="last_name"
                                        placeholder="{{ __('Enter Last Name')}}"
                                        x-ref="last_name"
                                        x-model="last_name"
                                        x-bind:class="{'outline-red-500': errors.last_name}"
                                        value="{{ old('last_name', $business->primaryUser?->last_name) }}" />
                                </div>
                                <div x-show="errors.last_name" x-text="errors.last_name" class="error-message-box"></div>
                                @error('last_name')<div class="error-message-box">{{ $message }}</div>@enderror
                            </div>

                            <div class="w-full">
                                <x-form.label for="email" required>{{__('Business Email Address')}}</x-form.label>

                                <div class="mt-1">
                                    <x-form.text
                                        name="email"
                                        placeholder="{{ __('Enter Email Address')}}"
                                        x-ref="email"
                                        x-model="email"
                                        x-bind:class="{'outline-red-500': errors.email}"
                                        value="{{ old('email', $user->email) }}"
                                        readonly />
                                </div>
                                <div x-show="errors.email" x-text="errors.email" class="error-message-box"></div>
                                @error('email')<div class="error-message-box">{{ $message }}</div>@enderror
                            </div>

                            <div class="w-full">
                                <x-form.label for="phone_number" required>{{__('Phone Number')}}</x-form.label>

                                <div class="mt-1">
                                    <x-form.text
                                        name="phone_number"
                                        placeholder="{{ __('Enter Phone Number')}}"
                                        x-ref="phone_number"
                                        x-model="phone_number"
                                        x-bind:class="{'outline-red-500': errors.phone_number}"
                                        value="{{ old('phone_number', $business->phone) }}" />
                                </div>
                                <div x-show="errors.phone_number" x-text="errors.phone_number" class="error-message-box"></div>
                                @error('phone_number')<div class="error-message-box">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                    <div class="onboard-steps-box-cards max-[767px]:!rounded-tl-none max-[767px]:!rounded-tr-none">
                        <div class="onboard-steps-box-cards-title mb-[24px]">
                            <img src="{{ asset('images/buisness-icon.svg') }}" alt="" class="icon">
                            <h2>{{ __('Business Details') }}</h2>
                        </div>
                        <div class="form-box">
                            <div class="w-full">
                                <x-form.label for="business_name" required>{{__('Business Name')}}</x-form.label>

                                <div class="mt-1">
                                    <x-form.text
                                        name="business_name"
                                        placeholder="{{ __('Enter Business Name')}}"
                                        x-ref="business_name"
                                        x-model="business_name"
                                        x-bind:class="{'outline-red-500': errors.business_name}"
                                        value="{{ old('business_name', $business->name) }}" />
                                </div>
                                <div x-show="errors.business_name" x-text="errors.business_name" class="error-message-box"></div>
                                @error('business_name')<div class="error-message-box">{{ $message }}</div>@enderror
                            </div>

                            <div class="w-full">
                                <x-form.label for="website_url">{{__('Website URL')}}</x-form.label>

                                <div class="mt-1">
                                    <x-form.text
                                        name="website_url"
                                        placeholder="{{ __('Enter Website URL')}}"
                                        x-ref="website_url"
                                        x-model="website_url"
                                        x-bind:class="{'outline-red-500': errors.website_url}"
                                        value="{{ old('website_url', $business->website_url) }}" />
                                </div>
                                <div x-show="errors.website_url" x-text="errors.website_url" class="error-message-box"></div>
                                @error('website_url')<div class="error-message-box">{{ $message }}</div>@enderror
                            </div>

                            <div class="w-full" x-data="dzUpload()" x-init="uploadImage()">
                                <x-form.label>{{__('Upload Business Logo')}}</x-form.label>

                                <div class="upload-box mt-1 cursor-pointer" id="dz-business-logo" x-ref="dzImageUpload" @click="$refs.dzImageUpload.click()">
                                    <div class="flex items-center justify-center w-full h-full">
                                        <img src="" alt="" id="dz-preview" class="max-w-full max-h-full object-contain" />
                                        <div id="dz-upload-btn" class="flex items-center justify-center flex-col w-full">
                                            <img src="{{ asset('images/upload-box-icon.svg') }}" alt="Upload icon" class="upload-icon">
                                            </svg>
                                            <div class="mt-4 flex text-gray-600 items-center text-[14px]">
                                                <span class="text-indigo-600 font-medium">{{__('Click to upload')}}</span>
                                                <p class="pl-1">{{__('or drag and drop')}}</p>
                                            </div>
                                            <p class="text-gray-600 items-center text-[14px]">{{__('PNG, JPG (max. 1024x1024px)')}}</p>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" name="logo" x-model="imageData" />
                                <div x-show="errors.fileError" x-text="errors.fileError" class="error-message-box"></div>
                                @error('logo')<div class="error-message-box">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="onbordstep-content-box">
                <div class="onboard-steps-box max-[600px]:!px-[0]" id="businessAddress" x-show="currentStep === 'businessAddress'" x-cloak>
                    <div class="onboard-steps-box-cards max-[767px]:p-[20px] max-[767px]:!rounded-tl-none max-[767px]:!rounded-tr-none">
                        <div class="onboard-steps-box-cards-title mb-[24px]">
                            <img src="{{ asset('images/nav-icon.svg') }}" alt="" class="icon">
                            <h2>{{ __('Business Address') }}</h2>
                        </div>
                        <div class="form-box">

                            <div class="w-full">
                                <x-form.label for="address" required>{{__('Address')}}</x-form.label>

                                <div class="mt-1">
                                    <x-form.text
                                        name="address"
                                        placeholder="{{ __('Enter Address')}}"
                                        x-ref="address"
                                        x-model="address"
                                        x-bind:class="{'outline-red-500': errors.address}"
                                        value="{{ old('address', $business->address) }}" />
                                </div>
                                <div x-show="errors.address" x-text="errors.address" class="error-message-box"></div>
                                @error('address')<div class="error-message-box">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-span-full">
                                <x-form.label for="street" required>{{__('Street')}}</x-form.label>

                                <div class="mt-1">
                                    <x-form.text
                                        name="street"
                                        placeholder="{{ __('Enter Street')}}"
                                        x-ref="street"
                                        x-model="street"
                                        x-bind:class="{'outline-red-500': errors.street}"
                                        value="{{ old('street', $business->street) }}" />
                                </div>
                                <div x-show="errors.street" x-text="errors.street" class="error-message-box"></div>
                                @error('street')<div class="error-message-box">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-span-full">
                                <x-form.label for="zipcode" required>{{__('Zip Code')}}</x-form.label>

                                <div class="mt-1">
                                    <x-form.text
                                        name="zipcode"
                                        placeholder="{{ __('Enter Zip Code')}}"
                                        x-ref="zipcode"
                                        x-model="zipcode"
                                        x-bind:class="{'outline-red-500': errors.zipcode}"
                                        value="{{ old('zipcode', $business->zipcode) }}" />
                                </div>
                                <div x-show="errors.zipcode" x-text="errors.zipcode" class="error-message-box"></div>
                                @error('zipcode')<div class="error-message-box">{{ $message }}</div>@enderror
                            </div>

                            <div class="grid grid-cols-3 w-full items-center justify-between gap-[20px] max-[600px]:grid-cols-1">
                                <div class="flex flex-col">
                                    <x-form.label for="country" required>{{__('Country')}}</x-form.label>

                                    <div class="mt-1 w-full">
                                        <select name="country"
                                            id="country"
                                            x-ref="country"
                                            x-model="country"
                                            @change="fetchStates()"
                                            value="{{ old('country', $business->country_id) }}"
                                            class="input-box">
                                            <option value="">{{__('Select Country')}}</option>
                                            @foreach ($countries as $country)
                                            <option value="{{ $country->id }}" {{ old('country',$business->country_id) == $country->id ? 'selected' : '' }}>{{ $country->name }}</option>
                                            @endforeach
                                        </select>

                                    </div>
                                    <div x-show="errors.country" x-text="errors.country" class="error-message-box"></div>
                                    @error('country')<div class="error-message-box">{{ $message }}</div>@enderror
                                </div>

                                <div class="flex flex-col">
                                    <x-form.label for="state" required>{{__('State')}}</x-form.label>

                                    <div class="mt-1 w-full">
                                        <select name="state"
                                            id="state"
                                            x-ref="state"
                                            x-model="state"
                                            @change="fetchCities()"
                                            value="{{ old('state', $business->state_id) }}"
                                            class="input-box">
                                            <option value="">{{__('Select State')}}</option>
                                            <template x-for="state in states" :key="state.id">
                                                <option :value="state.id" x-text="state.name" :selected="state.id == selectedState"></option>
                                            </template>
                                        </select>

                                    </div>
                                    <div x-show="errors.state" x-text="errors.state" class="error-message-box"></div>
                                    @error('state')<div class="error-message-box">{{ $message }}</div>@enderror
                                </div>

                                <div class="flex flex-col">
                                    <x-form.label for="city" required>{{__('City')}}</x-form.label>

                                    <div class="mt-1 w-full">
                                        <select name="city"
                                            id="city"
                                            x-ref="city"
                                            x-model="city"
                                            value="{{ old('city', $business->city_id) }}"
                                            class="input-box">
                                            <option value="">{{__('Select City')}}</option>
                                            <template x-for="city in cities" :key="city.id">
                                                <option :value="city.id" x-text="city.name" :selected="city.id == selectedCity"></option>
                                            </template>
                                        </select>

                                    </div>
                                    <div x-show="errors.city" x-text="errors.city" class="error-message-box"></div>
                                    @error('city')<div class="error-message-box">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-span-full">
                                <x-form.label for="timezone" required>{{__('Timezone')}}</x-form.label>

                                <div class="mt-1">
                                    <select
                                        name="timezone"
                                        x-ref="timezone"
                                        x-model="timezone"
                                        class="input-box"
                                        x-bind:class="{'outline-red-500': errors.timezone}"
                                        value="{{ old('timezone', $business->timezone) }}">
                                        <option value="">{{__('Select Timezone')}}</option>
                                        @foreach ($timezones as $timezone)
                                        <option value="{{ $timezone }}" {{ old('timezone', $business->timezone) == $timezone ? 'selected' : '' }}>{{ $timezone }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div x-show="errors.timezone" x-text="errors.timezone" class="error-message-box"></div>
                                @error('timezone')<div class="error-message-box">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="onboard-button-box">
                <x-form.button
                    type="button"
                    :label="__('Back')"
                    @click="currentStep = 'businessDetails'"
                    x-show="currentStep === 'businessAddress'"
                    class="btn-white  justify-center" />
                <span>{{ __('Already have an account?') }} <x-form.link :link="route('login')" class="font-[600]">{{ __('Sign In')}}</x-form.link></span>
                <x-form.button
                    type="button"
                    :label="__('Proceed')"
                    click="businessAddressStep()"
                    x-show="currentStep === 'businessDetails'"
                    class="btn justify-center" />
                <x-form.button
                    x-show="currentStep === 'businessAddress'"
                    x-bind:disabled="processing"
                    x-bind:class="{'opacity-75 cursor-not-allowed': processing}"
                    class="btn  justify-center">
                    {{ __('Proceed & Save')}}
                    <x-form.loading />
                </x-form.button>
            </div>

        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
    window.oldOnboarding = {
        country: "{{ old('country', $business->country_id) }}",
        state: "{{ old('state', $business->state_id) }}",
        city: "{{ old('city', $business->city_id) }}",
        timezone: "{{ old('timezone', $business->timezone) }}"
    };
</script>
@endsection
