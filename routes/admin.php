<?php

use App\Http\Controllers\Admin\AccountController;
use App\Http\Controllers\Admin\Auth\ForgotPasswordController;
use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\Auth\ResetPasswordController;
use App\Http\Controllers\Admin\BusinessController;
use App\Http\Controllers\Admin\ContentController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EarningController;
use App\Http\Controllers\Admin\SubAdminController;
use App\Http\Controllers\Admin\MassMessageController;
use App\Http\Controllers\Admin\SettingsController;

/**
 * Admin Routes
 *
 * All admin related routes are prefixed with 'admin' and named with 'admin.' prefix
 */
Route::prefix('admin')
    ->name('admin.')
    ->group(function (): void {

        // Guest Admin Routes
        Route::middleware(['pool.guest:admin'])->group(function (): void {
            Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');
            Route::get('/login', [LoginController::class, 'redirectToLogin']);
            Route::post('/login', [LoginController::class, 'login'])->name('login.submit');

            Route::name('password.')->group(function (): void {
                Route::get('/forgot-password', [ForgotPasswordController::class, 'create'])->name('request');
                Route::post('/forgot-password', [ForgotPasswordController::class, 'store'])->name('email');
                Route::get('/reset-password/{token}', [ResetPasswordController::class, 'create'])->name('reset');
                Route::post('/reset-password', [ResetPasswordController::class, 'store'])->name('update');
            });
        });

        // Protected Admin Routes
        Route::middleware('pool.auth:admin')->group(function (): void {

            Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
            Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
            Route::get('/manage-business-admin', [BusinessController::class, 'index'])->name('business.index');
            Route::get('/business-admin-profile/{id}', [BusinessController::class, 'show'])->name('business.show');
            Route::get('/earnings', [EarningController::class, 'index'])->name('earnings.index');

            Route::get('/content', [ContentController::class, 'index'])->name('content');
            Route::get('/account', [AccountController::class, 'index'])->name('account');
            Route::get('/mass-message', [MassMessageController::class, 'index'])->name('mass-message');
            Route::get('/setting', [SettingsController::class, 'index'])->name('setting');
            Route::post('/setting', [SettingsController::class, 'store'])->name('setting.store');

            Route::prefix('sub-admin')->group(function (): void {
                Route::get('/', [SubAdminController::class, 'index'])->name('sub-admin');
                Route::get('/create', [SubAdminController::class, 'create'])->name('sub-admin.create');
                Route::get('/edit/{user}', [SubAdminController::class, 'edit'])->name('sub-admin.edit');
            });

            Route::middleware(['signed'])->group(function (): void {
                Route::get(
                    '/business/auto-login/{user}',
                    [\App\Http\Controllers\Business\Auth\LoginController::class, 'autoLogin']
                )->name('business.auto-login');
            });
        });
    });
