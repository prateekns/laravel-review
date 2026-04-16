<?php

namespace App\Http\Middleware;

use App\Services\Business\SubscriptionService;
use Closure;
use Illuminate\Http\Request;

class CanSubscribe
{
    /**
     * The subscription service instance.
     *
     * @var SubscriptionService
     */
    protected $subscriptionService;

    /**
     * Create a new middleware instance.
     *
     * @return void
     */
    public function __construct(SubscriptionService $subscriptionService)
    {
        $this->subscriptionService = $subscriptionService;
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        if (!$this->subscriptionService->isStripeConfigured()) {
            return redirect()->route('account.index');
        }
        // Otherwise allow the request
        return $next($request);
    }
}
