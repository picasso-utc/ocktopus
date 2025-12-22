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

    Route::post('/refresh', [AuthController::class, 'refresh'])
        ->name('refresh');

    Route::get('/logout', [AuthController::class, 'logout'])
        ->name('logout');
});
