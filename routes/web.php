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

Route::get('/TV', [\App\Http\Controllers\TvController::class, 'index'])->name('TV.index');

Route::get('/TV/content', [\App\Http\Controllers\MediaController::class, 'content'])->name('TV.content');
Route::get('/TV/medias', [\App\Http\Controllers\MediaController::class, 'medias'])->name('TV.medias');

Route::get('/TV/medias/create',[\App\Http\Controllers\MediaController::class, 'create'])->name('media.create');
Route::post('/TV/medias/store',[\App\Http\Controllers\MediaController::class, 'store'])->name('media.store');

Route::get('/TV/medias/edit/{media}',[\App\Http\Controllers\MediaController::class, 'edit'])->name('media.edit');
Route::put('/TV/medias/update/{media}',[\App\Http\Controllers\MediaController::class, 'update'])->name('media.update');
Route::delete('/TV/medias/{media}',[\App\Http\Controllers\MediaController::class, 'destroy'])->name('media.destroy');

// ------------------------------------------------------------------------------------------------- //
// -----------------------------------Gestion des Liens------------------------------------------- //

Route::get('/TV/links', [\App\Http\Controllers\LinkController::class, 'links'])->name('TV.links');

Route::get('/TV/links/create',[\App\Http\Controllers\LinkController::class, 'create'])->name('link.create');
Route::post('/TV/links/store',[\App\Http\Controllers\LinkController::class, 'store'])->name('link.store');

Route::get('/TV/links/edit/{link}',[\App\Http\Controllers\LinkController::class, 'edit'])->name('link.edit');
Route::put('/TV/links/update/{link}',[\App\Http\Controllers\LinkController::class, 'update'])->name('link.update');
Route::delete('/TV/links/{link}',[\App\Http\Controllers\LinkController::class, 'destroy'])->name('link.destroy');

// ------------------------------------------------------------------------------------------------- //
// -----------------------------------Gestion des TVs------------------------------------------- //

Route::get('/TV/tvs', [\App\Http\Controllers\TvController::class, 'TVs'])->name('TV.tvs');
Route::get('/TV/create', [\App\Http\Controllers\TvController::class, 'create'])->name('TV.create');
Route::post('/TV/store', [\App\Http\Controllers\TvController::class, 'store'])->name('TV.store');
Route::get('/TV/edit/{tv}',[\App\Http\Controllers\TvController::class, 'edit'])->name('TV.edit');
Route::put('/TV/update/{tv}',[\App\Http\Controllers\TvController::class, 'update'])->name('TV.update');
Route::get('/TV/{tv}', [\App\Http\Controllers\TvController::class, 'show'])->name('TV.show');

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



Route::middleware(\App\Http\Middleware\Auth::class)->get('/test', function () {
    return view('welcome');
})->name("test");
Route::get('/auth',[\App\Http\Controllers\Connexion::class,'auth'])->name('auth_route');
Route::get('/userinfo', [\App\Http\Controllers\UserInfoController::class, 'getUserInfo']);
