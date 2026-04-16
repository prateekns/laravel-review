@extends('emails.layouts.master')

@section('title', 'Welcome to ' . config('app.name') . ' – Your Sub-admin Account Details')

@section('content')
<div style="padding: 30px 30px 10px 30px;">
    <p>Dear {{ $subAdmin->first_name }} {{ $subAdmin->last_name }},</p>

    <p>Welcome to {{ config('app.name') }}! Your sub-admin account has been created successfully.</p>

    <p>You can log in to the platform using the following credentials:</p>

    <div style="padding: 10px 0;">
        <p style="margin: 5px 0;"><strong>Email:</strong> {{ $subAdmin->email }}</p>
        <p style="margin: 5px 0;"><strong>Temporary Password:</strong> {{ $temporaryPassword }}</p>
    </div>

    <p>Please use the link below to access the login page:</p>
    <div>
        <a href="{{ route('login') }}">{{ route('login') }}</a>
    </div>

    <p>For security reasons, please change your password after your first login.</p>

    <p>If you have any questions or need assistance, feel free to reach out to our support team.</p>
</div>
@endsection
