@extends('emails.layouts.master')

@section('content')
<div style="padding: 30px 30px 10px 30px;">
    <h1>Subscription Updated</h1>
    
    <p>Dear {{ $customerName }},</p>
    
    <p>Your subscription has been successfully updated.</p>
    
    <div style="background: #f8f9fa;padding: 20px;margin: 20px 0;border-radius: 4px;">
        <h2>Updated Subscription Details:</h2>
        <ul style="list-style: none;padding: 0;">
            <li style="margin-bottom: 10px;">Number of Admins: {{ $business->num_admin }}</li>
            <li style="margin-bottom: 10px;">Number of Technicians: {{ $business->num_technician }}</li>
            <li style="margin-bottom: 10px;">Amount Paid: {{ $amount_paid }}</li>
        </ul>
    </div>

    <p>These changes are now active on your account. If you did not authorize this change or have any questions, please contact our support team immediately.</p>

    <div style="text-align: center; margin: 30px 0;">
        <a href="{{ route('account.index') }}">View Your Account</a>
    </div>

    <p>Thank you for being our valued customer!</p>
</div>
@endsection
