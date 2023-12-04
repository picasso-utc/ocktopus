<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GoodiesController;
use App\Http\Controllers\TopController;

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

Route::get('/get-goodies-winner', [GoodiesController::class, 'getWinner']);

Route::get('/get-top', [TopController::class, 'getTop']);
