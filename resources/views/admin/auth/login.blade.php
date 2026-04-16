@extends('layouts.admin.guest')
@section('title', __('admin.login'))
@section('content')
<div class="mt-10 sm:mx-auto sm:w-full sm:max-w-[480px]">
    @if (session('message'))
    <x-alert type="success" :message="session('message')" />
    @endif

    @if (session('error'))
    <x-alert type="error" :message="session('error')" />
    @endif

    @error('email')
    <x-alert type="error" :message="$message" />
    @enderror

    <div class="bg-white px-6 py-8 shadow-sm sm:rounded-lg sm:px-12">
        <form id="admin-login-form"
            x-data="loginForm"
            @submit.prevent="submitForm"
            class="space-y-6"
            action="{{ route('admin.login.submit') }}"
            method="POST">
            @csrf
            <div>
                <div class="flex items-center justify-between">
                    <label for="email" class="block text-sm/6 font-medium text-gray-900">{{ __('admin.label.email_address') }}</label>
                    <p x-show="errors.email" x-text="errors.email" class="mt-2 text-sm text-red-600"></p>
                </div>
                <div class="mt-2">
                    <input type="text"
                        name="email"
                        id="email"
                        x-model="email"
                        autocomplete="email"
                        class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6"
                        :class="{'outline-red-500': errors.email}"
                        value="{{ old('email') }}">
                </div>
            </div>

            <div>
                <div class="flex items-center justify-between">
                    <label for="password" class="block text-sm/6 font-medium text-gray-900">{{ __('admin.label.password') }}</label>
                    <p x-show="errors.password" x-text="errors.password" class="mt-2 text-sm text-red-600"></p>
                </div>
                <div class="mt-2">
                    <input type="password"
                        name="password"
                        id="password"
                        x-model="password"
                        autocomplete="current-password"
                        class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6"
                        :class="{'outline-red-500': errors.password}">
                </div>
            </div>

            <div class="flex items-center justify-between">
                <div class="flex gap-3">
                    <div class="flex h-6 shrink-0 items-center">
                        <div class="group grid size-4 grid-cols-1">
                            <input id="remember-me" name="remember" type="checkbox"
                                class="col-start-1 row-start-1 appearance-none rounded-sm border border-gray-300 bg-white checked:border-indigo-600 checked:bg-indigo-600 indeterminate:border-indigo-600 indeterminate:bg-indigo-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:border-gray-300 disabled:bg-gray-100 disabled:checked:bg-gray-100 forced-colors:appearance-auto">
                            <svg class="pointer-events-none col-start-1 row-start-1 size-3.5 self-center justify-self-center stroke-white group-has-disabled:stroke-gray-950/25" viewBox="0 0 14 14" fill="none">
                                <path class="opacity-0 group-has-checked:opacity-100" d="M3 8L6 11L11 3.5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                <path class="opacity-0 group-has-indeterminate:opacity-100" d="M3 7H11" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </div>
                    </div>
                    <label for="remember-me" class="block text-sm/6 text-gray-900">{{ __('admin.remember_me') }}</label>
                </div>

                <div class="text-sm/6">
                    <a href="{{ route('admin.password.request') }}" class="font-semibold text-indigo-600 hover:text-indigo-500">
                        {{ __('admin.forgot_password') }}
                    </a>
                </div>
            </div>

            <div>
                <button type="submit"
                    class="flex w-full justify-center cursor-pointer rounded-md bg-indigo-600 px-3 py-1.5 text-sm/6 font-semibold text-white shadow-xs hover:bg-indigo-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600" :disabled="submitted">
                    {{ __('admin.sign_in') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
