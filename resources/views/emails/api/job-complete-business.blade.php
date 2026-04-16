@extends('emails.layouts.master')

@section('content')
<div style="padding: 30px 30px 10px 30px;">
    <div style="background: #f8f9fa;border-radius: 4px;">
        <ul style="list-style: none;padding: 0;">
            <li style="margin-bottom: 10px;margin-left:0">Customer Name: {{ $workOrder->customer->customer_name }}</li>
            <li style="margin-bottom: 10px;margin-left:0">Customer Phone: {{ $workOrder->customer?->phone_1 ?? $workOrder->customer?->phone_2 ?? '' }}</li>
            <li style="margin-bottom: 10px;margin-left:0">Customer Email: {{ $workOrder->customer?->email_1 ?? $workOrder->customer?->email_2 ?? '' }}</li>
            <li style="margin-bottom: 10px;margin-left:0">Technician: {{ $workOrder->technician->fullName }}</li>
            <li style="margin-bottom: 10px;margin-left:0">Completion Date: {{ $jobCompletedAt }}</li>
        </ul>

        <div class="message">
            <h3>Communication Notes</h3>

            @if($message_business)
                <h4>Shared Details</h4>
                {!! nl2br(e($message_business)) !!}
            @endif
        </div>
        @if($attachment)
            <div class="attachment">
                <h3>Job Images</h3>
                @if(isset($attachment['business_image_1']))
                    <a href="{{ $attachment['business_image_1'] }}" target="_blank">
                        <img width="48" src="{{ $attachment['business_image_1_thumb'] }}" alt="Business Attachment 1">
                    </a>
                @endif
                @if(isset($attachment['business_image_2']))
                    <a href="{{ $attachment['business_image_2'] }}" target="_blank">
                        <img width="48" src="{{ $attachment['business_image_2_thumb'] }}" alt="Business Attachment 1">
                    </a>
                @endif
            </div>
        @endif
    </div>
    
    <p>This is an automated email from {{config('app.name')}}. Please do not reply.</p>
</div>
@endsection
