<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Traits\ApiResponse;
use App\Constants\ApiStatus;
use Laravel\Sanctum\PersonalAccessToken;

class CheckDevice
{
    use ApiResponse;

    public function handle(Request $request, Closure $next): Response
    {
        $deviceId = $request->header('X-Device-Id');

        if (!$deviceId) {
            return $this->errorResponse(__('api.missing_device_id'), 403, ApiStatus::MISSING_DEVICE_ID);
        }

        $tokenExists = PersonalAccessToken::where('device_id', $deviceId)->exists();

        if (!$tokenExists) {
            return $this->errorResponse(__('api.unauthorized_device'), 403, ApiStatus::UNAUTHORIZED_DEVICE);
        }
        return $next($request);
    }
}
