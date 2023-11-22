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

//**************************************************************************************************//
//*********************************Gestion des TVs*************************************************//
//*************************************************************************************************//


// ------------------------------------------------------------------------------------------------- //

// -----------------------------------Gestion des Médias------------------------------------------- //

Route::get('/TV/content', [\App\Http\Controllers\MediaController::class, 'content'])->name('TV.content');
Route::get('/TV/medias/', [\App\Http\Controllers\MediaController::class, 'medias'])->name('TV.medias');

Route::get('/TV/medias/create',[\App\Http\Controllers\MediaController::class, 'create'])->name('media.create');
Route::post('/TV/store',[\App\Http\Controllers\MediaController::class, 'store'])->name('media.store');

Route::get('/TV/medias/edit/{media}',[\App\Http\Controllers\MediaController::class, 'edit'])->name('media.edit');
Route::put('/TV/medias/{media}',[\App\Http\Controllers\MediaController::class, 'update'])->name('media.update');
Route::delete('/TV/medias/{media}',[\App\Http\Controllers\MediaController::class, 'destroy'])->name('media.destroy');

// ------------------------------------------------------------------------------------------------- //

// -----------------------------------Gestion des Liens------------------------------------------- //

//Route::get('/TV/medias', [\App\Http\Controllers\LinkController::class, 'liens'])->name('TV.liens');



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
