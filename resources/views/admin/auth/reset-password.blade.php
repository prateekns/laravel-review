@extends('layouts.admin.guest')
@section('title', __('admin.reset_password'))

@section('content')
<div class="mt-10 sm:mx-auto sm:w-full sm:max-w-[480px]">
@if (session('status'))
        <div class="mb-4 rounded-md bg-green-50 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">{{ session('status') }}</p>
                </div>
            </div>
        </div>
    @endif
    
    {{-- Display validation errors --}}
    @if ($errors->has('token'))
        <div class="mb-4 rounded-md bg-red-50 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-red-600">{{ $errors->first('token') }}</p>
                </div>
            </div>
        </div>
    @endif
    
    <div class="bg-white px-6 py-8 shadow-sm sm:rounded-lg sm:px-12">
        <form
        x-data="resetPassword"
           @submit.prevent="submitForm"
            class="space-y-6"
            action="{{ route('admin.password.update') }}"
            method="POST">
            @csrf
            <input type="hidden" name="token" value="{{ $request->route('token') }}">
            <input type="hidden" name="email" value="{{ old('email', $request->email) }}">

            <div>
                <h2 class="text-2xl font-semibold mb-1">{{ __('admin.reset_password') }}</h2>
                <p class="text-gray-600 mb-6">{{ __('admin.please_choose_a_new_password') }}</p>

                <p x-show="errors.password_rules" x-text="errors.password_rules" class="mb-2 text-sm text-red-600"></p>
                @error('password')
                    @if(str_contains($message, 'uppercase'))
                        <p class="mb-2 text-sm text-red-600">{{ $message }}</p>
                    @endif
                @enderror

                <div class="space-y-4">
                    <div>
                        <div class="flex items-center justify-between">
                            <label for="password" class="block text-sm/6 font-medium text-gray-900">{{ __('admin.label.new_password') }}</label>
                            <p x-show="errors.password" x-text="errors.password" class="mt-2 text-sm text-red-600"></p>
                            @error('password')
                                @if(!str_contains($message, 'uppercase'))
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @endif
                            @enderror
                        </div>
                        <div class="mt-2">
                            <input type="password"
                                name="password"
                                id="password"
                                x-model="password"
                                @input.debounce.300ms="validatePassword"
                                autocomplete="new-password"
                                placeholder="{{ __('admin.placeholder.new_password') }}"
                                class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6"
                                :class="{'!outline-red-500': errors.password || '{{ $errors->has('password') }}'}">
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center justify-between">
                            <label for="password_confirmation" class="block text-sm/6 font-medium text-gray-900">{{ __('admin.label.confirm_password') }}</label>
                            <p x-show="errors.password_confirmation" x-text="errors.password_confirmation" class="mt-2 text-sm text-red-600"></p>
                            @error('password_confirmation')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mt-2">
                            <input type="password"
                                name="password_confirmation"
                                id="password_confirmation"
                                x-model="password_confirmation"
                                @input.debounce.300ms="validateConfirmation"
                                autocomplete="new-password"
                                placeholder="{{ __('admin.placeholder.confirm_password') }}"
                                class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6"
                                :class="{'!outline-red-500': errors.password_confirmation}">
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <button type="submit"
                    class="flex w-full justify-center rounded-md bg-indigo-600 px-3 py-1.5 text-sm/6 font-semibold text-white shadow-xs hover:bg-indigo-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                    {{ __('admin.label.reset_password') }}
                </button>
            </div>

            
            <div class="text-sm text-center">
                <a href="{{ route('admin.login') }}" class="font-semibold text-indigo-600 hover:text-indigo-500">
                    {{ __('admin.back_to_login') }}
                </a>
            </div>

        </form>
    </div>
</div>
@endsection
