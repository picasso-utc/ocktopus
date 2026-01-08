<?php

use App\Http\Controllers\BachController;
use App\Http\Controllers\ExonerationController;
use App\Http\Controllers\TodayConsumptionController;
use App\Http\Controllers\TransactionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Application mobile
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Mobile\PermController;
use App\Http\Controllers\Mobile\AnnoncesController;
use App\Http\Controllers\Mobile\JeuxTemporaireController;
use App\Http\Controllers\Mobile\SemesterEventController;
use App\Http\Controllers\Mobile\BoiteIdeesController;
use App\Http\Controllers\Mobile\FaqController;
use App\Http\Controllers\Mobile\ShotgunController;

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
    Route::get('/perms', [PermController::class, 'index'])
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

    // Shotgun Events
    Route::get('/shotgun-events', [ShotgunController::class, 'index'])
        ->middleware('jwt');
    Route::post('/shotgun-events/toggle', [ShotgunController::class, 'toggle'])
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

    // Elo
    Route::prefix('elo')->middleware('jwt')->group(function () {
        Route::get('/get-rankings', [\App\Http\Controllers\Mobile\Elo::class, 'getRankings']);
        Route::get('/search-user', [\App\Http\Controllers\Mobile\Elo::class, 'searchUser']);
        Route::get('/get-user-elo', [\App\Http\Controllers\Mobile\Elo::class, 'getUserElo']);
        Route::get('/get-match-history', [\App\Http\Controllers\Mobile\Elo::class, 'getMarchHistory']);
        Route::get('/get-match-requests', [\App\Http\Controllers\Mobile\Elo::class, 'getMatchRequests']);
        Route::post('/create-match-record', [\App\Http\Controllers\Mobile\Elo::class, 'createMatchRecord']);
        Route::post('/respond-match-request', [\App\Http\Controllers\Mobile\Elo::class, 'respondMatch']);
    });
});
