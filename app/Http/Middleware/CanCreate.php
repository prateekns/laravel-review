<?php

namespace App\Http\Middleware;

use App\Services\Business\BusinessService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CanCreate
{
    /**
     * Create a new middleware instance.
     *
     * @return void
     */
    public function __construct(public BusinessService $businessService)
    {
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $feature): Response
    {
        if ($this->shouldRedirect($feature)) {
            return $this->getRedirectResponse($feature);
        }

        return $next($request);
    }

    /**
     * Determine if the request should be redirected based on feature limits.
     */
    private function shouldRedirect(string $feature): bool
    {
        return match($feature) {
            'admin' => $this->businessService->isAdminLimitReached(),
            'technician' => $this->businessService->isTechnicianLimitReached(),
            default => true,
        };
    }

    /**
     * Get the appropriate redirect response based on the feature.
     */
    private function getRedirectResponse(string $feature): Response
    {
        $route = match($feature) {
            'technician' => 'business.technicians.index',
            default => 'business.sub-admins.index',
        };

        return redirect()->route($route);
    }
}
