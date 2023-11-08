<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DefisController;

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

Route::get('/getWinnerStatus/{login}', [DefisController::class, 'isPickedUp']);
Route::post('/winner/select', [DefisController::class, 'getWinner']);

Route::get('/', function () {
    return view('welcome');
});
