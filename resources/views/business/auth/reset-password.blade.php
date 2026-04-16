@extends('layouts.business.guest')

@section('title', __('Reset Password'))
@section('heading', __('Reset Password'))
@section('subheading')
<p class="mt-2 text-sm/6 text-gray-500">
    {{__('Please choose a new password for your account.')}}
</p>
@endsection

@section('content')
<div class="mt-10 w-full">
    <div class="bg-white px-6 pl-0 py-8 shadow-sm sm:rounded-lg sm:px-12 max-[767px]:!shadow-none">
        <!-- Session Status -->
        @if (session('status'))
        <x-alert type="success" :message="session('status')" />
        @endif
        @if (session('error'))
        <x-alert type="error" :message="session('error')" />
        @endif


        <form method="POST"
            action="{{ route('password.update') }}"
            class="space-y-6"
            x-ref="form"
            x-data="resetPasswordForm"
            @submit.prevent="submitForm">
            @csrf
            <input type="hidden" name="token" value="{{ $request->route('token') }}">
            <input type="hidden" name="email" value="{{ old('email', $request->email) }}">

            <!-- Password Rule Error  -->

            <!-- Password Rule Error  -->

            <div>
                <div class="flex items-baseline space-x-2">
                    <x-form.label id="password">{{__('New Password')}}</x-form.label>

                </div>
                <div class="mt-2">
                    <x-form.text
                        type="password"
                        name="password"
                        x-model="password"
                        placeholder="{{ __('Enter New Password')}}"
                        ::class="{'outline-red-500': errors.password || errors.password_rule}" />
                </div>
                <div x-show="errors.password_rule" x-text="errors.password_rule" class="error-message-box"></div>
                @error('password')
                @if(str_contains($message, 'uppercase'))
                <div class="error-message-box">{{ $message }}</div>
                @endif
                @enderror


                @error('password')
                @if(!str_contains($message, 'uppercase'))
                <div x-show="showPhp" class="mt-2 text-sm text-red-600">{{ $message }}</div>
                @endif
                @enderror
                <div x-show="errors.password" x-text="errors.password" class="error-message-box"></div>
            </div>

            <div>
                <div class="flex items-baseline space-x-2">
                    <x-form.label id="password_confirmation">{{__('Confirm Password')}}</x-form.label>

                </div>
                <div class="mt-2">
                    <x-form.text
                        type="password"
                        name="password_confirmation"
                        x-model="password_confirmation"
                        placeholder="{{ __('Confirm New Password')}}"
                        ::class="{'outline-red-500': errors.password_confirmation}" />
                </div>
                @error('password_confirmation')<div x-show="showPhp" class="error-message-box">{{ $message }}</div>@enderror
                <div x-show="errors.password_confirmation" x-text="errors.password_confirmation" class="error-message-box"></div>
            </div>

            <div>
                <x-form.button
                    x-bind:disabled="processing"
                    x-bind:class="{'opacity-75 cursor-not-allowed': processing}"
                    class="btn-box btn items-center justify-center w-full">
                    {{ __('Reset Password')}}
                    <x-form.loading />
                </x-form.button>
            </div>
        </form>
    </div>
</div>
@endsection
