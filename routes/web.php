<?php

use Illuminate\Support\Facades\Route;

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


Route::get('/perm', [\App\Http\Controllers\PermController::class, 'index'])->name('perm.index');
Route::get('/perm/perms', [\App\Http\Controllers\PermController::class, 'perms'])->name('perm.perms');

Route::get('/perm/create', [\App\Http\Controllers\PermController::class, 'create'])->name('perm.create');
Route::post('/perm/store', [\App\Http\Controllers\PermController::class, 'store'])->name('perm.store');
Route::delete('/perm/delete/{perm}',[\App\Http\Controllers\PermController::class, 'destroy'])->name('perm.destroy');
