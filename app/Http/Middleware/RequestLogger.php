<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class RequestLogger
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): Response
    {
        $requestData = [
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'headers' => $request->headers->all(),
            'body' => $request->all(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ];

        // Remove sensitive data like passwords and tokens
        if (isset($requestData['body']['password'])) {
            $requestData['body']['password'] = '[REDACTED]';
        }
        if (isset($requestData['headers']['authorization'])) {
            $requestData['headers']['authorization'] = '[REDACTED]';
        }

        // Log the request
        Log::channel('requests')->info('Incoming Request', $requestData);

        return $next($request);
    }
}
