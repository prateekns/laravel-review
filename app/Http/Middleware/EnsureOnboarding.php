<?php

namespace App\Http\Middleware;

use App\Services\Business\BusinessService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureOnboarding
{
    /**
     * The business service instance.
     *
     * @var BusinessService
     */
    protected $businessService;

    /**
     * Create a new middleware instance.
     *
     * @return void
     */
    public function __construct(BusinessService $businessService)
    {
        $this->businessService = $businessService;
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $isOnboardingRoute = $request->route()->getName() === 'onboarding';
        $needsOnboarding = $this->businessService->needsOnboarding();

        if ($this->shouldRedirect($isOnboardingRoute, $needsOnboarding)) {
            return $this->getRedirectResponse($needsOnboarding);
        }

        return $next($request);
    }

    /**
     * Determine if the request should be redirected.
     */
    private function shouldRedirect(bool $isOnboardingRoute, bool $needsOnboarding): bool
    {
        return ($needsOnboarding && !$isOnboardingRoute) ||
               (!$needsOnboarding && $isOnboardingRoute);
    }

    /**
     * Get the appropriate redirect response.
     */
    private function getRedirectResponse(bool $needsOnboarding): Response
    {
        return redirect()->route($needsOnboarding ? 'onboarding' : 'dashboard');
    }
}
