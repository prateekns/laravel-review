@extends('emails.layouts.master')

@section('content')
    <div style="padding: 30px 30px 10px 30px;">
        <h2>Hi {{ $name }}!</h2>
        
        <p>You have been granted Sub-Admin access by {{ config('app.name') }}.</p>

        <p>Please click the link below to sign in on the platform:</p>
        
        <a href="{{ route('admin.login') }}">{{ route('admin.login') }}</a>

        <div class="credentials">
            <p> Your credentials:</p>
            <p style="margin: 0;"><strong>Email:</strong> {{ $email }}</p>
            <p style="margin: 0;"><strong>Password:</strong> {{ $password }}</p>
        </div>
        
        <p>For your security, please change your password after your first login.</p>
    </div>
@endsection
