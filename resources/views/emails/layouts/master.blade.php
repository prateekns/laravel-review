<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Subscription Notification')</title>
</head>
<body style="background-color: #fff;font-family: Arial, sans-serif;line-height: 1.6;color: #333;margin: 0;padding: 0;">
    <div style="max-width: 600px;margin: 20px auto;background: #f8f9fa;border-radius: 8px;box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);">
        <div style="background: #2d3748;color: #ffffff;padding: 20px;text-align: center;border-radius: 8px 8px 0 0;">
            <h2>{{ config('app.name') }}</h2>
        </div>

        @yield('content')

        <div style="padding:0 30px;font-family: Arial, sans-serif;line-height: 1.6;">
            <p>Thank you,<br>{{ config('app.name') }}</p>
        </div>

        <div style="text-align: center;padding: 20px;color: #718096;font-size: 0.9em;border-top: 1px solid #e2e8f0;">
            <p>© {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
