@extends('emails.layouts.master')
@section('content')
    <div style="padding: 30px 30px 10px 30px;font-family: Arial, sans-serif;line-height: 1.6;">
        <p>Hello {{ $name }},</p>
        <p>We received a request to reset your password.</p>
        <p>To reset your password, click the link below:</p>
        <div><a href="{{ $url }}">Reset My Password</a></div>
        <p>This link will expire in 1Hr. and can only be used once.</p>
        <p>If you didn’t request this, please ignore this message.</p>
    </div>
@endsection
