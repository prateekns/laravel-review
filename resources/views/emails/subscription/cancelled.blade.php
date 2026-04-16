@extends('emails.layouts.master')

@section('content')
<div style="padding: 30px 30px 10px 30px;">
    <h1>Subscription Cancelled</h1>
    
    <p>Dear {{ $customerName }},</p>
    
    @if($cancelAfterPeriodEnd)
        <p>We're sorry to see you go. Your subscription will be cancelled at the end of the current billing period as requested.</p>
    @else
        <p>We're sorry to see you go. Your subscription has been cancelled as requested.</p>
    @endif
    
    <div style="background: #f8f9fa;padding: 20px;margin: 20px 0;border-radius: 4px;">
        <h2>Cancellation Details:</h2>
        <ul style="list-style: none;padding: 0;">
            <li style="margin-bottom: 10px;">Subscription ID: {{ $subscriptionId }}</li>
            <li style="margin-bottom: 10px;">Cancellation Date: {{ $cancelDate }}</li>
            <li style="margin-bottom: 10px;">Subscription End Date: {{ $endDate }}</li>
        </ul>
    </div>

    @if($cancelAfterPeriodEnd)
        <p>You will continue to have access to our services until {{ $endDate }}. After this date, your access will be discontinued.</p>

        <p>If you cancelled by mistake or would like to reactivate your subscription, you can do so from your account page before the end date.</p>
        
    @endif

    <p>We value your feedback. If you'd like to share why you cancelled or how we could improve our service, please let us know.</p>

    <p>Thank you for being our customer. We hope to serve you again in the future!</p>
</div>
@endsection
