<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $guard): Response
    {
        if ($guard === 'admin' && Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        } elseif ($guard === 'business' && Auth::guard('business')->check()) {
            return redirect()->route('dashboard');
        }
        return $next($request);
    }
}
