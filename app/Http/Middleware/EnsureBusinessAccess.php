<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureBusinessAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $businessUser = Auth::guard('business')->user();

        foreach ($request->route()->parameters() as $param) {
            if (is_object($param) && isset($param->business_id)) {
                $businessId = $param->getAttribute('business_id');

                if ($businessId && $businessId !== $businessUser->business_id) {
                    \Log::warning('[EnsureBusinessAccess] Access denied', [
                        'route' => $request->route()->getName(),
                        'param_business_id' => $businessId,
                        'user_business_id' => $businessUser->business_id,
                    ]);
                    abort(403, 'You do not have access to this resource.');
                }
            }
        }

        return $next($request);
    }
}
