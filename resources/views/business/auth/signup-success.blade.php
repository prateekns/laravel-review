@extends('layouts.business.guest')
@section('title', __('Signup Success'))
@section('body_class', 'signup-success')
@section('content')
<div class="mt-10">
    <div class="flex flex-col items-center justify-center white-box max-[767px]:!shadow-none">
        <div><x-icons name="blue-tick"/></div>
        <h2 class="font-[32px] font-[700] text-black-primary text-center">{{ __('Account Created Successfully') }}</h2>
        <div class="bg-white p-6 pt-0 mt-[11px]">
            <p class="text-xs text-grey-50 mt-[11px] text-center">
            {{ __('Account created successfully. We have sent you an email to your inbox. You can verify your account to log in to the platform.')}}
            </p>
        </div>
    </div>
@endsection
</div>
