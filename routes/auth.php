<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| Auth routes (OAuth / CAS)
|--------------------------------------------------------------------------
*/

// =====================
// AUTH MOBILE
// =====================
Route::prefix('mobile/auth')->name('mobile.auth.')->group(function () {

    Route::get('/login', [AuthController::class, 'login'])
        ->name('login');

    Route::get('/callback', [AuthController::class, 'callback'])
        ->name('callback');

    Route::match(['GET', 'POST'], '/refresh', [AuthController::class, 'refresh'])
        ->name('refresh');

    Route::get('/logout', [AuthController::class, 'logout'])
        ->name('logout');
});

// pages de fin de flow pour RN WebView
Route::prefix('mobile')->name('mobile.')->group(function () {
    Route::get('/api-connected', function () {
        return response('OK');
    })->name('api-connected');

    Route::get('/api-not-connected', function () {
        return response('NOT OK');
    })->name('api-not-connected');
});
