<?php

namespace App\Providers;

use App\Models\Business\Business;
use App\Observers\BusinessObserver;
use App\Services\Business\Payment\PaymentInterface;
use App\Services\Business\Payment\Stripe;
use App\Services\Business\Scheduler\DateTimeService;
use App\Services\Business\Scheduler\TechnicianScheduleService;
use App\Services\Business\Scheduler\JobAssignmentService;
use App\Services\Business\Scheduler\ExportService;
use App\View\Composers\AppDataComposer;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Laravel\Cashier\Cashier;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(PaymentInterface::class, Stripe::class);

        // Register scheduler services
        $this->app->singleton(DateTimeService::class);
        $this->app->singleton(TechnicianScheduleService::class);
        $this->app->singleton(JobAssignmentService::class);
        $this->app->singleton(ExportService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Business::observe(BusinessObserver::class);
        Cashier::useCustomerModel(Business::class);
        // Register view composer for business layout
        View::composer('layouts.business.partials.header', AppDataComposer::class);
    }
}
