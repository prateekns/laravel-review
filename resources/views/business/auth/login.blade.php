@extends('layouts.business.guest')

@section('title', 'Login')
@section('heading', __('Welcome Back'))
@section('subheading')
    <p class="mt-2 text-sm/6 text-gray-500 flex justify-center">
        {{ __('Access your dashboard with your account details') }}
    </p>
@endsection

@section('content')
    <div class="login-form-box">

        @if (request()->has('expired'))
            <x-alert type="error" :message="__('common.message.session_expired')" />
        @endif

        <!-- Session Status -->
        @if (session('status'))
            <x-alert type="success" :message="session('status')" />
        @endif

        @if (session('error'))
            <x-alert type="error" :message="session('error')" />
        @endif
        <div class="register-form-box">
            <form method="POST" action="{{ route('login.submit') }}" class="space-y-6" x-data="loginForm" x-ref="form"
                @submit.prevent="submitForm">
                @csrf
                <div>
                    <div class="flex items-baseline space-x-2">
                        <x-form.label>{{ __('Email Address') }}</x-form.label>

                    </div>
                    <div class="mt-2">
                        <x-form.text name="email" placeholder="{{ __('Enter Email Address') }}" x-model="email"
                            x-bind:class="{ 'outline-red-500': errors.email }" />
                    </div>
                    @error('email')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <div x-show="errors.email" x-text="errors.email" class="error-message-box" x-clock></div>
                </div>

                <div>
                    <div class="flex items-baseline space-x-2">
                        <x-form.label>{{ __('Password') }}</x-form.label>

                    </div>
                    <div class="mt-2">
                        <x-form.text type="password" name="password" x-model="password" x-ref="password"
                            placeholder="{{ __('Enter Password') }}"
                            x-bind:class="{ 'outline-red-500': errors.password }" />
                    </div>
                    @error('password')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <div x-show="errors.password" x-text="errors.password" class="error-message-box" x-clock></div>
                </div>

                <div class="remember-me-box">
                    <div class="remember-checkbox-wrapper input-checkbox">
                        <x-form.checkbox name="remember" value="1" />
                        <x-form.label>{{ __('Remember me') }}</x-form.label>
                    </div>

                    <div class="forgot-password-link">
                        <x-form.link :link="route('password.request')">{{ __('Forgot password?') }}</x-form.link>
                    </div>
                </div>

                <div class="account-sign-up-wrapper">
                    <x-form.button x-bind:disabled="processing"
                        x-bind:class="{ 'opacity-75 cursor-not-allowed': processing }" class="btn w-full justify-center">
                        {{ __('Sign In') }}
                        <x-form.loading />
                    </x-form.button>
                    <div class="sign-up-text mt-[24px]">
                        {{ __('Don\'t you have an account?') }}
                        <x-form.link :link="route('register')" class="font-[600]">
                            {{ __('Sign up') }}
                        </x-form.link>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
