<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Pool Route Notification')</title>
    <style>
        body {
            background-color: #FFFFFF;
            margin: 0;
            padding: 0;
        }
    </style>
</head>

<body style="background-color: #FFFFFF;margin: 0;padding: 0;font-family:Arial, Helvetica, sans-serif">

    <table border="0" style="background-color: #ffffff;width:100%" aria-describedby="JobCompletionEmail">
        <thead>
            <tr><th></th></tr>
        </thead>
        <tr>
            <td>
                <table border="0" style="max-width: 650px;margin: 20px auto;background: #ffffff;border-radius: 8px;width:650px" aria-describedby="JobCompletionEmailLayout">
                    <thead>
                        <tr><th></th></tr>
                    </thead>
                    <!-- Header -->
                    <tr>
                        <td style="text-align:center">
                            @if($businessLogoUrl)
                                <img alt="Business Logo" src="{{ $businessLogoUrl }}" width="120" style="margin-top:5px;border-radius: 50%;"/>
                            @else
                            <h2>{{$workOrder->business->name}}</h2>
                            @endif
                        </td>
                    </tr>

                    <!-- Content -->
                    <tr>
                        <td>
                            @yield('content')
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="padding: 20px 0;">
                            <p style="margin: 0;font-family: Arial, sans-serif; color: #000000;font-size:16px;font-weight:400;">You can reply to this email as it will go to your pool company.</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 0 0 20px;">
                            <p style="margin: 0 0 10px 0; font-family: Arial, sans-serif; color: #000000;font-size:16px;font-weight:400;line-height:20px;">Thank you,<br>Pool Route</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align: center;padding: 20px; border-top: 1px solid #BBBDBF; font-size: 16px; color: #000000;line-height:20px;">
                            <p style="margin: 0;font-family: Arial, sans-serif;font-size:16px;color:#000000;font-weight:400;line-height:18px">© {{ date('Y') }} Pool Route | <a href="{{config('app.url')}}">{{config('app.url')}}</a></p>
                            <p style="margin: 8px 0;font-family: Arial, sans-serif;font-size:16px;color:#000000;font-weight:700;line-height:18px">All rights reserved</p>
                            <p style="width:48px;display:block;">
                                <img alt="Footer Logo" src="{{ asset('images/email-footer-logo.png') }}" style="width: 100%;margin: 0 auto;display: block;"/>
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
