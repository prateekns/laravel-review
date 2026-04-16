@extends('emails.layouts.master')

@section('content')
<div style="padding: 30px 30px 10px 30px;">
    <h1>Payment Successful!</h1>
    
    <p>Dear {{ $customerName }},</p>
    
    <p>We've received your payment. Thank you!</p>
    
    <div style="background: #f8f9fa; padding: 20px; margin: 20px 0; border-radius: 4px;">
        <h2>Payment Details:</h2>
        <ul style="list-style: none;padding: 0;">
            <li style="margin-bottom: 10px;">Invoice Number: {{ $invoiceNumber }}</li>
            <li style="margin-bottom: 10px;">Amount Paid: {{ $currency }} {{ $amountPaid }}</li>
        </ul>
    </div>

    <div>
        <p>You can:</p>
        <div style="text-align: center; margin: 15px 0;">
            <a href="{{ $hostedInvoiceUrl }}">View Invoice Online</a>
            <a href="{{ $invoicePdf }}" style="margin-left:10px">Download PDF Invoice</a>
        </div>
    </div>

    <p>If you have any questions about this payment, please don't hesitate to contact our support team.</p>

    <div style="text-align: center; margin: 30px 0;">
        <a href="{{ route('account.index') }}">View Your Account</a>
    </div>

    <p>Thank you for your continued business!</p>
</div>
@endsection
