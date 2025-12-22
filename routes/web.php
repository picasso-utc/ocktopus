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

// Compresseur d'images pour les bornes
Route::get('/compress', [\App\Http\Controllers\ImageProxyController::class, 'compress']);

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


/*
// Gestion de l'authentification CAS pour l'application mobile
use App\Http\Controllers\AuthController;
// Authentification CAS — APPLICATION MOBILE UNIQUEMENT
Route::prefix('mobile')->group(function () {

    // Lance le flow CAS (appelé par l’app RN)
    Route::get('/auth/login', [AuthController::class, 'login'])
        ->name('mobile.auth.login');

    // Callback CAS (appelé par le provider OAuth/CAS)
    Route::get('/auth/callback', [AuthController::class, 'callback'])
        ->name('mobile.auth.callback');

    // Retour SUCCÈS vers l’app
    Route::get('/api-connected', function (\Illuminate\Http\Request $request) {
        return response()->json([
            'access_token'  => $request->query('access_token'),
            'refresh_token' => $request->query('refresh_token'),
        ]);
    })->name('mobile.api-connected');

    // Retour ERREUR vers l’app
    Route::get('/api-not-connected', function (\Illuminate\Http\Request $request) {
        return response()->json([
            'error'   => true,
            'message' => $request->query('message'),
        ], 401);
    })->name('mobile.api-not-connected');

});
*/
