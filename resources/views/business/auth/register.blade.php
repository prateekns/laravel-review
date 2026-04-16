@extends('layouts.business.guest')
@section('title', 'Register')
@section('body_class', 'register')
@section('subheading')
<h2 class="h2">{{__('Welcome, Let\'s Get You Started')}}</h2>
<p class="mt-2 text-sm/6 text-gray-500 flex justify-center">
    {{ __('Just a few steps away from effortless operations')}}
</p>
@endsection

@section('content')
<div class="login-form-box">
    <div class="register-form-box">
        <form
            x-data="businessRegistrationForm" x-ref="form"
            @submit.prevent="submitForm"
            method="POST"
            action="{{ route('register.submit') }}"
            class="space-y-4">

            <!-- Business Name -->
            <div>
                <div class="flex items-baseline space-x-2">
                    <x-form.label>{{ __('Business Name')}}</x-form.label>

                </div>
                <div class="mt-2">
                    <x-form.text
                        name="business_name"
                        placeholder="{{ __('Enter Business Name')}}"
                        x-ref="business_name"
                        x-model="business_name"
                        x-bind:class="{'outline-red-500': errors.business_name}" />
                </div>
                @error('business_name')<div class="error-message-box">{{ $message }}</div>@enderror
                <div x-show="errors.business_name" class="error-message-box" x-text="errors.business_name"></div>
            </div>

            <!-- Email Address -->
            <div>
                <div class="flex items-baseline space-x-2">
                    <x-form.label>{{ __('Email Address')}}</x-form.label>

                </div>
                <div class="mt-2">
                    <x-form.text
                        name="email"
                        placeholder="{{ __('Enter Email Address')}}"
                        x-ref="email"
                        x-model="email"
                        x-bind:class="{'outline-red-500': errors.email}" />
                </div>
                @error('email')<div class="error-message-box">{{ $message }}</div>@enderror
                <div x-show="errors.email" class="error-message-box" x-text="errors.email"></div>
            </div>

            <!-- Password -->
            <div>
                <div class="flex items-baseline space-x-2">
                    <x-form.label>{{ __('Password')}}</x-form.label>

                </div>
                <div class="mt-2">
                    <x-form.text
                        name="password"
                        placeholder="{{ __('Enter Password')}}"
                        x-ref="password"
                        x-model="password"
                        type="password"
                        x-bind:class="{'outline-red-500': errors.password || errors.password_rule}" />
                </div>
                @csrf
                <div x-show="errors.password_rule" class="error-message-box" x-text="errors.password_rule"></div>
                @error('password')
                @if(str_contains($message, 'uppercase'))
                <div class="error-message-box">{{ $message }}</div>
                @endif
                @enderror

                @error('password')
                @if(!str_contains($message, 'uppercase'))
                <div class="error-message-box">{{ $message }}</div>
                @endif
                @enderror
                <div x-show="errors.password" class="error-message-box" x-text="errors.password"></div>
            </div>

            <!-- Confirm Password -->
            <div>
                <div class="flex items-baseline space-x-2">
                    <x-form.label>{{ __('Confirm Password')}}</x-form.label>

                </div>
                <div class="mt-2">
                    <x-form.text
                        name="password_confirmation"
                        placeholder="{{ __('Re-enter Password')}}"
                        x-ref="password_confirmation"
                        x-model="password_confirmation"
                        type="password"
                        x-bind:class="{'outline-red-500': errors.password_confirmation}" />
                </div>
                @error('password_confirmation')<div class="error-message-box">{{ $message }}</div>@enderror
                <div x-show="errors.password_confirmation" class="error-message-box" x-text="errors.password_confirmation"></div>
            </div>

            <div class="account-sign-up-wrapper">
                <x-form.button
                    x-bind:disabled="processing"
                    x-bind:class="{'opacity-75 cursor-not-allowed': processing}"
                    class="btn w-full justify-center">
                    {{ __('Create Account')}}
                    <x-form.loading />

                </x-form.button>
                <div class="account-sign-up-wrapper">
                    <div class="sign-up-text mt-[24px]">
                        <span> {{ __('Already have an account?')}}</span>
                        <x-form.link :link="route('login')" class="font-[600]">{{ __('Sign In')}}</x-form.link>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
