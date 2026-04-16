@extends('emails.layouts.master')
@section('content')
    <div style="padding: 30px 30px 10px 30px;font-family: Arial, sans-serif;line-height: 1.6;">
        <h1 style="color: #333;margin-bottom: 20px;">Hi {{ $business->name }},</h1>

        <p>Thank you for signing up on {{ config('app.name') }}. Your account has been successfully created.</p>

        <p>To activate your access, please complete the required onboarding details using the link below:</p>
        
        <div style="display:inline-flex;align-items:center;gap:10px;">
            <p>Login to Your Account: <a href="{{ $verificationUrl }}">Click here to log in</a></p>
        </div>

        <p>Next Steps:</p>

        <ol style="padding-left: 15px;margin: 20px 0;">
            <li style="margin: 10px 0;">Log in using your registered credentials.</li>
            <li style="margin: 10px 0;">Complete the mandatory profile form to proceed.</li>
            <li style="margin: 10px 0;">Once submitted, you’ll gain full access to your Business Admin dashboard.</li>
        </ol>

        <p>If you have any questions or need assistance, please don't hesitate to contact us.</p>
    </div>
@endsection
