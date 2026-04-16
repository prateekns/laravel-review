@extends('emails.layouts.master')

@section('title', 'Welcome to ' . config('app.name') . ' – Your Technician Account Details')

@section('content')
<div style="padding: 30px 30px 10px 30px;">
    <p>Hi {{ $technician->first_name }},</p>

    <p>Welcome to {{ config('app.name') }}!</p>

    <p>Your technician account has been created. You can now log in to the mobile app using the credentials below:</p>

    <div style="background: #f8f9fa; padding: 10px 0; border-radius: 4px;">
        <p style="margin: 0;"><strong>Staff ID:</strong> {{ $technician->staff_id }}</p>
        <p style="margin: 0;"><strong>Password:</strong> {{ $password }}</p>
    </div>

    <p>Please log in and change your password immediately after your first login for security purposes.</p>

    <p>Need help getting started? Contact your Business Admin.</p>
</div>
@endsection
