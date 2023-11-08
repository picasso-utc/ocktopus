<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GoodiesController;

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

Route::get('/getWinnerStatus/{login}', [GoodiesController::class, 'isPickedUp']);
Route::post('/winner/select', [GoodiesController::class, 'getWinner']);

Route::get('/', function () {
    return view('welcome');
});
