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

Route::get('/TV', [\App\Http\Controllers\MediaController::class, 'index'])->name('TV.index');
Route::get('/TV/content', [\App\Http\Controllers\MediaController::class, 'content']);
Route::get('/TV/create',[\App\Http\Controllers\MediaController::class, 'create'])->name('media.create');
Route::post('/TV/store',[\App\Http\Controllers\MediaController::class, 'store'])->name('media.store');



// ------------------------------------------------------------------------------------------------- //

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
