@extends('layouts.business.guest')
@section('title', __('Reset Password Success'))
@section('body_class', 'email-sent-success')
@section('content')
<div class="mt-10">
    <div class="flex flex-col items-center justify-center  white-box">
        <div><x-icons name="blue-tick"/></div>
        <h2 class="font-[32px] font-[700] text-black-primary text-center">{{ __('Email Sent') }}</h2>
        <div class="bg-white p-6 pt-0 mt-[11px]">
            <p class="text-xs text-grey-50 mt-[11px] text-center">
            {{ __('We’ve sent a password reset link to your email. Please check your inbox and follow the instructions to reset your password.')}}
            </p>
        </div>
    </div>
@endsection
</div>
