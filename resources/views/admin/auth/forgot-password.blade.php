@extends('layouts.admin.guest')
@section('title', __('label.admin.form.forgot_password_heading'))

@section('content')
<div class="mt-10 sm:mx-auto sm:w-full sm:max-w-[480px]">
    @if (session('status'))
        <x-alert type="success" :message="session('status')"/>
    @endif

    {{-- Display validation errors --}}
    @if ($errors->any())
        @foreach ($errors->all() as $error)
            <x-alert type="error" :message="$error"/>
        @endforeach
    @endif

    <div class="bg-white px-6 py-8 shadow-sm sm:rounded-lg sm:px-12">
        <form
            x-data="forgotPassword"
            x-ref="form"
            @submit.prevent="submitForm"
            class="space-y-6"
            action="{{ route('admin.password.email') }}"
            method="POST">
            @csrf

            <div>
                <h2 class="text-2xl font-semibold mb-6">{{ __('admin.forgot_password_heading') }}</h2>
                <p class="text-gray-600 mb-6">{{ __('admin.forgot_password_description') }}</p>
                
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
                        placeholder="{{ __('admin.placeholder.email_address') }}"
                        class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6"
                        :class="{'!outline-red-500': errors.email || '{{ $errors->has('email') }}'}"
                        value="{{ old('email') }}">
                </div>
            </div>

            <div>
                <button type="submit"
                    class="flex w-full justify-center rounded-md bg-indigo-600 px-3 py-1.5 text-sm/6 font-semibold text-white shadow-xs cursor-pointer hover:bg-indigo-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                    {{ __('admin.send_reset_link') }}
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
