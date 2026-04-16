<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Traits\ApiResponse;
use App\Constants\ApiStatus;

class VerifyAppSignature
{
    use ApiResponse;

    public function handle(Request $request, Closure $next): Response
    {
        $message = '';
        // Validate the request signature and headers.
        $signature = $request->header('X-App-Signature');
        $deviceId = $request->header('X-Device-Id');
        $timestamp = $request->header('X-App-Timestamp');

        if (! $signature || ! $deviceId || ! $timestamp) {
            return $this->errorResponse(__('api.missing_signature_headers'), 401, ApiStatus::MISSING_SIGNATURE_HEADERS);
        }

        // Check if the timestamp is within 5 minutes (300 seconds)
        if (abs(time() - (int) $timestamp) > 300) {
            $message = __('api.signature_expired');
            $errorCode = ApiStatus::SIGNATURE_EXPIRED;
        }

        $secret = config('app.signature_secret');
        $expectedSignature = hash_hmac('sha256', $deviceId.'|'.$timestamp, $secret);

        if (! hash_equals($expectedSignature, $signature)) {
            $message = __('api.invalid_signature');
            $errorCode = ApiStatus::INVALID_SIGNATURE;
        }

        if ($message) {
            return $this->errorResponse($message, 401, $errorCode);
        }

        return $next($request);
    }
}
