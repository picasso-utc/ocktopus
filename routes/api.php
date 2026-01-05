<?php

use App\Http\Controllers\BachController;
use App\Http\Controllers\ExonerationController;
use App\Http\Controllers\TodayConsumptionController;
use App\Http\Controllers\TransactionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Configuration du middleware
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Authentification via CAS
Route::post('/bach/login/cas',[BachController::class, 'loginCas']);

// Authentification via badgeuse
Route::post('/bach/login/badge',[BachController::class, 'loginBadge']);

// Récupération des ventes journalières d'un ou plusieurs produits
//Route::get('/get-sales/{productNames}', [TodayConsumptionController::class, 'getSales']);
Route::get('/get-sales/{productName}', [TodayConsumptionController::class, 'getTodayConsumption']);


// Récupération des id d'utilisateurs bloqués
Route::get('/blocages', [\App\Http\Controllers\BlocageController::class, 'getBlocages']);

// Exoneration d'un achat
Route::post('/exoneration', [ExonerationController::class, 'storeExonerations']);

Route::post('/transaction', [TransactionController::class, 'handle']);

// Application mobile
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Mobile\PermController;
use App\Http\Controllers\Mobile\AnnoncesController;
use App\Http\Controllers\Mobile\JeuxTemporaireController;
use App\Http\Controllers\Mobile\SemesterEventController;
use App\Http\Controllers\Mobile\BoiteIdeesController;
use App\Http\Controllers\Mobile\FaqController;

Route::prefix('mobile')->group(function () {

    // L'app React Native appelle GET /auth/me  =>  /api/mobile/auth/me
    Route::get('/auth/me', [AuthController::class, 'getUserData'])
        ->middleware('jwt');

    // L'app React Native refresh => POST /auth/refresh  =>  /api/mobile/auth/refresh
    Route::post('/auth/refresh', [AuthController::class, 'refresh'])
        ->middleware('jwt');

    // Demande de permanence
    Route::post('/perms', [PermController::class, 'store'])
        ->middleware('jwt');

    // Permanences de la semaine en cours
    Route::get('/perms/current-week', [PermController::class, 'currentWeek'])
        ->middleware('jwt');

    // Annonces
    Route::get('/annonces', [AnnoncesController::class, 'index'])
        ->middleware('jwt');

    // FAQs
    Route::get('/faqs', [FaqController::class, 'index'])
        ->middleware('jwt');

    // Jeux Temporaires
    Route::get('/jeux-temporaires', [JeuxTemporaireController::class, 'index'])
        ->middleware('jwt');

    // Évènements du semestre
    Route::get('/semester-events', [SemesterEventController::class, 'index'])
        ->middleware('jwt');

    // Boite à Idées
    Route::get('/boite-idees', [BoiteIdeesController::class, 'index'])
        ->middleware('jwt');
    Route::post('/boite-idees', [BoiteIdeesController::class, 'store'])
        ->middleware('jwt');
});
