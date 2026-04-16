<?php

namespace App\View\Composers;

use App\Services\Business\BusinessService;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class AppDataComposer
{
    /**
     * Create a new app data composer.
     */
    public function __construct(protected BusinessService $businessService)
    {
    }

    /**
     * Bind data to the view.
     *
     * @return void
     */
    public function compose(View $view)
    {
        $isTrialActive = false;
        $trialEndsIn = null;
        $planEndsIn = null;
        if (Auth::guard('business')->check()) {
            $isTrialActive = $this->businessService->isTrialActive();
            $trialEndsIn = $this->businessService->trialEndsIn();
            $planEndsIn = $this->businessService->planEndsIn();
        }
        $view->with('isTrialActive', $isTrialActive);
        $view->with('trialEndsIn', $trialEndsIn);
        $view->with('planEndsIn', $planEndsIn);
    }
}
