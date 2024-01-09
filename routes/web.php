<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

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

Route::get('/', function () {
    return view('welcome');
});

// ---------------------------Téléchargement de fichier image------------------------------------- //
Route::prefix('/image')->group(function () {
    //get image from ?url=
    Route::get('/', function (Request $request) {
        $url = $request->query('url');
        //avoid path traversal
        if (str_contains($url, '..')) {
            return response()->json(['message' => 'Invalid url'], 400);
        }
        //in storage folder
        $path = storage_path('app/public/' . $url);
        if (File::exists($path)) {
            return response()->file($path);
        }
        return response()->json(['message' => 'Image not found'], 404);
    })->name('image');
});

Route::middleware(\App\Http\Middleware\Auth::class)->get('/test', function () {
    return view('welcome');
})->name("test");
Route::get('/auth',[\App\Http\Controllers\Connexion::class,'auth'])->name('auth_route');
Route::get('/logout',[\App\Http\Controllers\Connexion::class,'logout'])->name('logout_route');
Route::get('/userinfo', [\App\Http\Controllers\UserInfoController::class, 'getUserInfo']);

Route::get('/get-goodies-winner', [\App\Http\Controllers\GoodiesController::class, 'getWinner'])->name('payutc.goodiesWinner');
