<?php

use Illuminate\Support\Facades\Route;
use App\Models\Country;
use App\Models\State;
use App\Http\Controllers\Api\V1\TechnicianAuthController;
use App\Http\Controllers\Api\V1\TechnicianJobController;
use App\Http\Controllers\Api\V1\TechnicianProfileController;
use App\Http\Controllers\Api\V1\NotificationController;

// Public routes
Route::get('/states/{country}', function (Country $country) {
    return $country->states()->select('id', 'name')->where('status', State::ACTIVE)->get();
});

Route::get('/cities/{state}', function (State $state) {
    return $state->cities()->select('id', 'name')->get();
});

Route::prefix('v1')->group(function () {

    Route::middleware(['verifyAppSignature'])->group(function () {
        Route::post('/check-device', [TechnicianAuthController::class, 'checkDeviceSwitch']);
        Route::post('/check-device-otp-login', [TechnicianAuthController::class, 'checkDeviceSwitchForOtpLogin']);
        Route::post('/refresh-token', [TechnicianAuthController::class, 'refreshToken'])->name('refresh.token');
        Route::post('/login', [TechnicianAuthController::class, 'login'])->name('login');
        Route::post('/password/reset-request', [TechnicianAuthController::class, 'requestLoginOtp'])->middleware('throttle:2,1');
        Route::post('/password/resend-reset-request', [TechnicianAuthController::class, 'requestLoginOtp'])->middleware('throttle:2,1')->name('otp.resend');
        Route::post('/password/verify-reset-otp', [TechnicianAuthController::class, 'doOtpLogin'])->name('otp.login');
    });

    Route::middleware(['auth:sanctum', 'technician','checkDevice'])->group(function () {
        Route::post('/auth/invalidate', [TechnicianAuthController::class, 'logout']);
        Route::get('/profile', [TechnicianProfileController::class, 'getProfile']);
        Route::post('/change-password', [TechnicianProfileController::class, 'changePassword']);
        Route::get('/jobs', [TechnicianJobController::class, 'index']);
        Route::get('/jobs/{id}', [TechnicianJobController::class, 'show']);
        Route::post('/jobs/complete/{id}', [TechnicianJobController::class, 'completeJob']);

        // Notification routes
        Route::prefix('notification')->group(function () {
            Route::post('/sms-to-customer', [NotificationController::class, 'sendSmsToCustomer']);
            Route::post('/notify-customer-business', [NotificationController::class, 'notifyCustomerBusiness']);
        });
    });
});
