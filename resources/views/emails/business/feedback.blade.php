@extends('emails.layouts.master')

@section('title', 'Business Feedback')

@section('content')
<div style="padding: 30px; 30px 10px 30px;">
    <div style="background-color: #f7fafc;border-radius: 8px;padding: 20px;">
        <p style="margin-bottom: 10px;"><strong>Business Name:</strong> {{ $business->name }}</p>
        <p style="margin-bottom: 10px;"><strong>Business Email:</strong> {{ $business->email }}</p>
        <h3 style="color: #2d3748;margin-bottom: 15px;">Feedback Message:</h3>
        <p style="white-space: pre-wrap;">{{ $feedbackMessage }}</p>
    </div>
</div>
@endsection
