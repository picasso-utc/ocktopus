<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\TodayConsumptionController;
use App\Http\Controllers\GetSalesController;

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

// Configuration du middleware
Route::middleware(\App\Http\Middleware\Auth::class)->get('/test', function () {
    return view('welcome');
})->name("test");


// Gestion des télés
Route::get('/TV/content', [\App\Http\Controllers\MediaController::class, 'content'])->name('TV.content');
Route::get('/TV/{tv}', [\App\Http\Controllers\TvController::class, 'show'])->name('TV.show');

// Téléchargement de fichier image
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

// Gestion de l'authentification
Route::get('/auth',[\App\Http\Controllers\Connexion::class,'auth'])->name('auth_route');
Route::get('/logout',[\App\Http\Controllers\Connexion::class,'logout'])->name('logout_route');

// Téléchargement de fichier général
Route::get('/download/{filename}', function ($filename) {
    $filename = str_replace('..', '', $filename);
    if (Storage::exists('files/' . $filename)) {
        return Storage::download('files/' . $filename);
    } else {
        return response()->json(['message' => 'Image not found'], 404);
    }
})->name('download');

Route::get('/bourse',[\App\Http\Controllers\TransactionController::class,'getPrices']);
