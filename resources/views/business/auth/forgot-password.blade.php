@extends('layouts.business.guest')

@section('title', __('Forgot Password'))
@section('heading', __('Forgot Password'))
@section('subheading')
<p class="mt-2 text-sm/6 text-gray-500">
{{__('Enter your email below to receive an email to reset your password')}}
</p>
@endsection

@section('content')
<div class="login-form-box">
    <div class="register-form-box">
        <!-- Session Status -->
        @if (session('status'))
            <x-alert type="success" :message="session('status')" />
        @endif

        @if (session('error'))
            <x-alert type="error" :message="session('error')" />
        @endif

        <form method="POST"
            action="{{ route('password.email') }}"
            x-ref="form"
            class="space-y-6"
            x-data="forgotPasswordForm"
            @submit.prevent="submitForm">
            @csrf
            
            <div>
                <div class="flex items-baseline space-x-2">
                    <x-form.label id="email">{{ __('Email Address*')}}</x-form.label>
                </div>
                <div class="mt-2">
                     <x-form.text
                        name="email"
                        placeholder="{{ __('Enter Email Address')}}"
                        x-model="email"
                        ::class="{'outline-red-500': errors.email}" />
                </div>
                 @error('email')<div x-show="showPhp" class="error-message-box">{{ $message }}</div>@enderror
                <div x-show="errors.email" x-text="errors.email" class="error-message-box" x-cloak></div>
            </div>

            <div class="account-sign-up-wrapper">
                <x-form.button
                    x-bind:disabled="processing"
                    x-bind:class="{'opacity-75 cursor-not-allowed': processing}"
                    class="btn w-full justify-center">
                    {{ __('Send Email')}}
                    <x-form.loading/>
                </x-form.button>

                <x-form.link :link="route('login')" class="btn-box outlined w-full justify-center mt-4">{{ __('Back to Login')}}</x-form.link>
            </div>
            </div>
        </form>
    </div>
</div>
@endsection
