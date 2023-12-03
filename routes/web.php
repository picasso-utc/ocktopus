<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiPayutcController;
use App\Http\Controllers\GoodiesController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/',function () {
    return view('welcome');
});

Route::middleware(\App\Http\Middleware\Auth::class)->get('/test', function () {
    return view('welcome');
})->name("test");

Route::get('/auth',[\App\Http\Controllers\Connexion::class,'auth'])->name('auth_route');

Route::get('/userinfo', [\App\Http\Controllers\UserInfoController::class, 'getUserInfo']);

Route::get('/payutc', function () {
    return view('payutc');
});

Route::get('/get-goodies-winner', [GoodiesController::class, 'getWinner']);
