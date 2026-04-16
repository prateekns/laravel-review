<?php

use App\Http\Controllers\Shared\AppController;
use Illuminate\Support\Facades\Route;

// Temporary url
Route::get('run/{command}', function ($command) {
    $allowedCommands = [
        'optimize',
        'clear-compiled',
        'cache:clear',
        'view:clear',
        'route:clear',
        'cache:forget',
        'config:cache',
        'config:clear',
        'optimize:clear',
        'package:discover',
        'queue:restart',

    ];
    if (! in_array($command, $allowedCommands)) {
        abort(404);
    }
    \Artisan::call($command);

    return \Artisan::output();
});

// Admin Routes
require __DIR__.'/admin.php'; //NOSONAR
// Business Routes
require __DIR__.'/business.php'; //NOSONAR

// Test route - remove after testing
Route::get('/workorders-test', function () {
    return 'Work Orders Index Page Test Route is working!';
})->name('business.workorders.test');

// Add this outside all middleware groups for testing
Route::get('/business/workorders-direct', function () {
    return view('business.workorders.index');
})->name('direct.workorders');

Route::get('/states/{country_id}', [AppController::class, 'getStates']);
Route::get('/cities/{state_id}', [AppController::class, 'getCities']);
