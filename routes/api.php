<?php

use App\Http\Controllers\BachController;
use App\Http\Controllers\ExonerationController;
use App\Http\Controllers\TodayConsumptionController;
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
Route::get('/get-sales/{productNames}', [TodayConsumptionController::class, 'getSales']);

// Récupération des id d'utilisateurs bloqués
Route::get('/blocages', [\App\Http\Controllers\BlocageController::class, 'getBlocages']);

// Exoneration d'un achat
Route::post('/exoneration', [ExonerationController::class, 'storeExonerations']);
