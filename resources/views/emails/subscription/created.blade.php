@extends('emails.layouts.master')

@section('content')
<div style="padding: 30px 30px 10px 30px;">
    <h1>Welcome to Our Service!</h1>
    
    <p>Dear {{ $customerName }},</p>
    
    <p>Thank you for subscribing to our service! Your subscription is now active and ready to use.</p>
    
    <div style="background: #f8f9fa;padding: 20px;margin: 20px 0;border-radius: 4px;">
        <h2>Subscription Details:</h2>
        <ul style="list-style: none;padding: 0;">
            <li style="margin-bottom: 10px;">Number of Admins: {{ $business->num_admin }}</li>
            <li style="margin-bottom: 10px;">Number of Technicians: {{ $business->num_technician }}</li>
            <li style="margin-bottom: 10px;">Amount Paid: {{ $amount_paid }}</li>
        </ul>
    </div>

    <p>If you have any questions about your subscription or need assistance, please don't hesitate to contact our support team.</p>
    
    <div style="text-align: center; margin: 30px 0;"><a href="{{ route('account.index') }}">View Your Account</a></div>

    <p>Thank you for choosing our service!</p>
</div>
@endsection
