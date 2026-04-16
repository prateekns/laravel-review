<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class LogStoreRequest
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        Log::info('LogStoreRequest middleware starting', [
            'method' => $request->method(),
            'path' => $request->path(),
            'data' => $request->all()
        ]);

        try {
            $response = $next($request);

            Log::info('LogStoreRequest middleware after controller', [
                'response_type' => get_class($response),
                'response_headers' => $response->headers->all()
            ]);

            return $response;
        } catch (\Exception $e) {
            Log::error('Error in middleware chain', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
}
