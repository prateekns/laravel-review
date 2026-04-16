<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTechnicianUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if the authenticated user's token belongs to a Technician
        if ($request->user() && get_class($request->user()) === 'App\Models\Business\Technician\Technician') {
            return $next($request);
        }

        return response()->json([
            'success' => false,
            'message' => 'Unauthorized. This route is only accessible to technicians.',
        ], 403);
    }
}
