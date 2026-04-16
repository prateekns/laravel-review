<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Business\AccountController;
use App\Http\Controllers\Business\Auth\ForgotPasswordController as BusinessForgotPasswordController;
use App\Http\Controllers\Business\Auth\LoginController as BusinessLoginController;
use App\Http\Controllers\Business\Auth\RegisterController as BusinessRegisterController;
use App\Http\Controllers\Business\Auth\ResetPasswordController as BusinessResetPasswordController;
use App\Http\Controllers\Business\Auth\VerificationController;
use App\Http\Controllers\Business\BusinessController as BusinessBusinessController;
use App\Http\Controllers\Business\ChecklistController;
use App\Http\Controllers\Business\ChemicalCalculatorController;
use App\Http\Controllers\Business\DashboardController as BusinessDashboardController;
use App\Http\Controllers\Business\OnBoardingController;
use App\Http\Controllers\Business\SubAdminController;
use App\Http\Controllers\Business\TechnicianController;
use App\Http\Controllers\Business\WorkOrder\WorkOrderController;
use App\Http\Controllers\Business\ItemsSoldController;
use App\Http\Controllers\Business\Template\TemplateController;
use App\Http\Controllers\Business\CustomersController;
use App\Http\Controllers\Business\WorkOrder\MaintenanceOrdersController;
use App\Http\Controllers\Business\Scheduler\ScheduleController;
use App\Http\Controllers\Business\ReportsController;
use App\Http\Controllers\Business\PagesController;

const WORK_ORDER_PATH = '/{workOrder}';
const CHECKLIST_ITEMS_PATH = '/items/{id}';

// Guest Business Routes
Route::middleware('pool.guest:business')->group(function () {
    Route::get('/', function () {
        return view('business.index');
    });
    Route::get('/signin', [BusinessLoginController::class, 'showLoginForm'])->name('login');
    Route::post('/signin', [BusinessLoginController::class, 'login'])->name('login.submit');
    Route::get('/signup', [BusinessRegisterController::class, 'create'])->name('register');
    Route::post('/signup', [BusinessRegisterController::class, 'store'])->name('register.submit');
    Route::get('/signup/success', [BusinessRegisterController::class, 'success'])->name('signup.success');

    // Password Reset Routes
    Route::name('password.')->group(function (): void {
        Route::get('/forgot-password', [BusinessForgotPasswordController::class, 'create'])->name('request');
        Route::post('/forgot-password', [BusinessForgotPasswordController::class, 'store'])->name('email');
        Route::get('/forgot-password/success', [BusinessForgotPasswordController::class, 'resetLinkSuccess'])->name('success');
        Route::get('/reset-password/{token}', [BusinessResetPasswordController::class, 'create'])->name('reset');
        Route::post('/reset-password', [BusinessResetPasswordController::class, 'store'])->name('update');
    });
});

Route::prefix('business')->group(function () {
    // Email Verification Routes (accessible without authentication)
    Route::get('/email/verify', [VerificationController::class, 'notice'])->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])->name('verification.verify');
    Route::post('/email/resend', [VerificationController::class, 'resend'])->name('verification.resend');

    // Protected Business Routes
    Route::middleware(['pool.auth:business'])->group(function () {
        Route::post('/logout', [BusinessLoginController::class, 'logout'])->name('logout');

        // Routes that should check for subscription
        Route::middleware('subscription.active')->group(function () {
            // Premium features that require subscription
        });

        //Complete profile post route for businesses, keep this outside of onboarding middleware
        Route::post('/complete-profile', [OnBoardingController::class, 'storeOnboarding'])->name('onboarding.store');

        // Routes that should check for onboarding
        Route::middleware('onboarding')->group(function () {
            // Service CRUD under manage work orders
            Route::resource('items-sold', ItemsSoldController::class)->names('business.items-sold');

            // Work Orders Routes
            Route::prefix('work-orders')->name('business.work-orders.')->group(function () {
                Route::get('/', [WorkOrderController::class, 'index'])->name('index');
                Route::get('/create', [WorkOrderController::class, 'create'])->name('create');
                Route::get('/customer/{customer}/create', [WorkOrderController::class, 'create'])->name('customer.create');

                // Direct test route
                Route::post('/direct-test', function () {
                    dd('Direct test route hit');
                })->name('direct-test');

                Route::post('/', [WorkOrderController::class, 'store'])->name('store');
                Route::get('/{instance_id}/view/completed', [WorkOrderController::class, 'showAssignmentCompleted'])->name('show_assignment_completed');
                Route::get(WORK_ORDER_PATH . '/view', [WorkOrderController::class, 'show'])->name('show');
                Route::get(WORK_ORDER_PATH . '/edit', [WorkOrderController::class, 'edit'])->name('edit');
                Route::put(WORK_ORDER_PATH, [WorkOrderController::class, 'update'])->name('update');
                Route::delete(WORK_ORDER_PATH, [WorkOrderController::class, 'destroy'])->name('destroy');

                // Checklist routes
                Route::prefix('checklist')->name('checklist.')->group(function () {
                    Route::get('/', [ChecklistController::class, 'index'])->name('index');
                    Route::get('/items', [ChecklistController::class, 'getItems'])->name('items');
                    Route::post('/items', [ChecklistController::class, 'store'])->name('store');
                    Route::get(CHECKLIST_ITEMS_PATH, [ChecklistController::class, 'show'])->name('show');
                    Route::put(CHECKLIST_ITEMS_PATH, [ChecklistController::class, 'update'])->name('update');
                    Route::delete(CHECKLIST_ITEMS_PATH, [ChecklistController::class, 'destroy'])->name('destroy');
                    Route::get('/items/by/service', [ChecklistController::class, 'getChecklistItemsByServiceType'])->name('items.by.service');
                });

                // Maintenance Work Orders Routes
                Route::get('/maintenance', [MaintenanceOrdersController::class, 'index'])->name('maintenance.index');
                Route::get('/maintenance/create', [MaintenanceOrdersController::class, 'create'])->name('maintenance.create');
                Route::get('/maintenance/customer/{customer}/create', [MaintenanceOrdersController::class, 'create'])->name('maintenance.customer.create');
                Route::post('/maintenance', [MaintenanceOrdersController::class, 'store'])->name('maintenance.store');
                Route::get('/maintenance/{maintenance}/view', [MaintenanceOrdersController::class, 'show'])->name('maintenance.show');
                Route::get('/maintenance/{maintenance}/edit', [MaintenanceOrdersController::class, 'edit'])->name('maintenance.edit');
                Route::put('/maintenance/{maintenance}', [MaintenanceOrdersController::class, 'update'])->name('maintenance.update');
                Route::get('/maintenance/{instance_id}/view/completed', [MaintenanceOrdersController::class, 'showAssignmentCompleted'])->name('maintenance.show_assignment_completed');
            });

            //Complete profile get route for businesses, keep this inside of onboarding middleware
            Route::get('/complete-profile', [OnBoardingController::class, 'onboarding'])->name('onboarding');
            Route::get('/dashboard', [BusinessDashboardController::class, 'index'])->name('dashboard');
            Route::get('/help', [BusinessBusinessController::class, 'help'])->name('help');
            Route::post('/profile/update', [BusinessBusinessController::class, 'updateProfile'])->name('business.profile.update');
            Route::get('/profile/change-password', [BusinessBusinessController::class, 'changePassword'])->name('profile.change-password');

            // Customer Routes
            Route::prefix('customers')->group(function () {
                Route::get('/', [CustomersController::class, 'index'])->name('business.customers.index');
                Route::get('/add', [CustomersController::class, 'create'])->name('business.customers.create');
                Route::post('/', [CustomersController::class, 'store'])->name('business.customers.store');
                Route::get('/import', [CustomersController::class, 'showImport'])->name('business.customers.import');
                Route::post('/import', [CustomersController::class, 'import'])->name('business.customers.import.process');
                Route::get('/import/sample', [CustomersController::class, 'downloadSample'])->name('business.customers.import.sample');
                Route::get('/{customer}/view', [CustomersController::class, 'show'])->name('business.customers.show');
                Route::get('/{customer}/edit', [CustomersController::class, 'edit'])->name('business.customers.edit');
                Route::put('/{customer}', [CustomersController::class, 'update'])->name('business.customers.update');
            });

            // Chemical List Routes
            Route::get('/chemical-list', [ChemicalCalculatorController::class, 'chemicalList'])->name('business.chemical-list');

            // My Account Routes
            Route::prefix('account')->name('account.')->group(function () {
                Route::get('/pricing', [AccountController::class, 'index'])->name('index');
                Route::get('/profile', [AccountController::class, 'index'])->name('profile');
                Route::get('/my-plan', [AccountController::class, 'index'])->name('my-plan');
                Route::middleware('canSubscribe')->group(function () {
                    Route::get('/plan-selection', [AccountController::class, 'showCreatePricing'])->name('pricing.create');
                    Route::post('/plan-selection', [AccountController::class, 'createOrder'])->name('team.pricing.store');
                    Route::get('/payment-summary/{payment_uuid}', [AccountController::class, 'paymentForm'])->name('payment.form');

                    Route::get('/downgrade-summary', [AccountController::class, 'showDowngradePlan'])->name('plan.downgrade');
                    Route::post('/downgrade-summary', [AccountController::class, 'downgradePlan'])->name('downgrade.store');
                    Route::get('/downgrade-summary/{payment_uuid}', [AccountController::class, 'downgradeSummary'])->name('downgrade.summary');
                    Route::post('/downgrade', [AccountController::class, 'processDowngrade'])->name('process.downgrade');
                    Route::get('/downgrade/success/{order_id}', [AccountController::class, 'downgradeSuccess'])->name('downgrade.success');
                });
            });

            Route::middleware('canSubscribe')->group(function () {
                Route::post('/payment/intent', [AccountController::class, 'createSetupIntent'])->name('payment.intent');
                Route::post('/payment/process', [AccountController::class, 'processPayment'])->name('payment.process');
                Route::get('/account/payment-success/{payment_uuid}', [AccountController::class, 'paymentSuccess'])->name('payment.success');
                Route::post('/stripe/price/create', [AccountController::class, 'createPrice'])->name('stripe.create.price');

                Route::get('/stripe/proration', [AccountController::class, 'prorationCheck'])->name('stripe.proration');
                Route::post('/stripe/update-card', [AccountController::class, 'updatePaymentMethod'])->name('stripe.update.card');
                Route::post('/stripe/cancel-subscription', [AccountController::class, 'cancelSubscription'])->name('stripe.cancel.subscription');
                Route::post('/stripe/rollback-subscription', [AccountController::class, 'rollbackSubscription'])->name('stripe.rollback.subscription');
            });

            // Technician routes - base routes accessible to all
            Route::get('/technicians/{technician}/future-jobs', [TechnicianController::class, 'checkFutureJobs'])->name('business.technicians.future-jobs');

            // Chemical Calculator Routes
            Route::get('/chemical-calculator', [ChemicalCalculatorController::class, 'index'])->name('chemical-calculator.index');
            Route::post('/chemical-calculator/calculate', [ChemicalCalculatorController::class, 'calculate'])->name('chemical-calculator.calculate');

            // Sub-admin management routes
            Route::prefix('user-management')->group(function () {
                // Protected create/store routes
                Route::get('/sub-admins/add', [SubAdminController::class, 'create'])->name('business.sub-admins.create');
                // Open routes
                Route::get('/sub-admins', [SubAdminController::class, 'index'])->name('business.sub-admins.index');
                Route::get('/sub-admins/edit/{subAdmin}', [SubAdminController::class, 'edit'])->name('business.sub-admins.edit');

                Route::get('/technicians/add', [TechnicianController::class, 'create'])->name('business.technicians.create');
                Route::post('/technicians', [TechnicianController::class, 'store'])->name('business.technicians.store');

                Route::resource('technicians', TechnicianController::class)->except(['create', 'store'])->names('business.technicians')->middleware('ensure.business.access');
            });

            // Add templates management routes here
            Route::prefix('templates')->name('templates.')->group(function () {
                Route::get('/', TemplateController::class)->name('index');
                Route::get('/{template}/edit', [TemplateController::class, 'edit'])->name('edit');
            });

            // Items Sold routes
            Route::prefix('items-sold')->name('items-sold.')->group(function () {
                Route::get('/', [ItemsSoldController::class, 'index'])->name('index');
            });

            // Reports routes
            Route::prefix('reports')->name('reports.')->group(function () {
                Route::get('/', [ReportsController::class, 'index'])->name('index');
                Route::get('/export-excel', [\App\Http\Controllers\Business\ReportExcelController::class, 'download'])->name('export-excel');
            });

            // Manage Work Schedule
            Route::get('/scheduler', [ScheduleController::class, 'index'])->name('business.scheduler.index');
            Route::get('/scheduler/{workOrder}/assign', [ScheduleController::class, 'assign'])->name('business.scheduler.assign');
            Route::get('/scheduler/export-jobs/{technicianId}/{date}', [ScheduleController::class, 'exportJobs'])->name('scheduler.export-jobs');
            Route::post('/schedule/assign-job', [ScheduleController::class, 'assignJob'])->name('business.schedule.assign-job');

            // Checklist Management
            Route::get('/manage-checklist', ChecklistController::class)->name('business.checklists.index');

            // Checklist routes
            Route::get('/workorders/template/{templateId}/checklist', [WorkOrderController::class, 'getTemplateChecklist'])
                ->name('workorders.template.checklist');
            Route::get('/workorders/template/{templateId}/checklist/form', [WorkOrderController::class, 'showChecklistForm'])
                ->name('workorders.template.checklist.form');
            Route::post('/workorders/checklist', [WorkOrderController::class, 'storeChecklist'])
                ->name('workorders.checklist.store');
        });
    });
});

// Client address fetch route
Route::get('/business/clients/{client}/address', [WorkOrderController::class, 'getClientAddress'])->name('business.clients.address');
// Pages routes
Route::get('/privacy-policy', [PagesController::class, 'privacyPolicy'])->name('privacy-policy');
