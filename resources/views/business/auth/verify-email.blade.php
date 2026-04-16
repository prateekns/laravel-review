@extends('layouts.business.guest')
@section('title', 'Verify Email')
@section('body_class', 'verify-email')
@section('heading', 'Verify Your Email Address')
@section('subheading')

<div class="mt-2">
    @if (session('error'))
        <x-alert type="error" :message="session('error')"/>
    @endif

    @if (session('status'))
        <x-alert type="success" :message="session('status')"/>
    @endif
</div>

<div class="bg-white p-6 shadow-sm sm:rounded-lg mt-4">
<p class="text-sm/6 text-gray-500">
   {{ __('Account created successfully. We have sent you an email to your inbox. You can verify your account to log in to the platform.')}}
</p>
@endsection

@section('content')
<div class="mt-10">
    <div>
        <form method="POST" action="{{ route('verification.resend') }}" class="space-y-6">
            @csrf
            <input type="hidden" name="email" value="{{ session('email') }}">
            <div>
                <button type="submit" class="btn btn-box w-full">
                    Resend Verification Email
                </button>
            </div>
        </form>
        <div class="mt-6 text-center text-sm font-normal">
            <x-form.link :link="route('login')">{{ __('Back to Login')}}</x-form.link>
        </div>
    </div>
</div>
@endsection
</div>
