<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/Picsous',[App\Http\Controllers\Treso\MainController::class, 'index'])->name('Picsous.index');

Route::get('/Picsous/facturerecues', [App\Http\Controllers\Treso\FactureRecueController::class, 'facturerecue'])->name('Picsous.facturerecue');
Route::get('/Picsous/facturerecues/info/{factureRecue}', [App\Http\Controllers\Treso\FactureRecueController::class, 'facturerecueInfo'])->name('Picsous.facturerecue.facturerecueInfo');

Route::get('/Picsous/facturerecues/create',[App\Http\Controllers\Treso\FactureRecueController::class, 'create'])->name('Picsous.facturerecue.create');
Route::post('/Picsous/facturerecues/store',[App\Http\Controllers\Treso\FactureRecueController::class, 'store'])->name('Picsous.facturerecue.store');

Route::get('/Picsous/facturerecues/edit/{factureRecue}',[App\Http\Controllers\Treso\FactureRecueController::class, 'edit'])->name('Picsous.facturerecue.edit');
Route::put('/Picsous/facturerecues/update/{factureRecue}',[App\Http\Controllers\Treso\FactureRecueController::class, 'update'])->name('Picsous.facturerecue.update');
Route::delete('/Picsous/facturerecues/{factureRecue}',[App\Http\Controllers\Treso\FactureRecueController::class, 'destroy'])->name('Picsous.facturerecue.destroy');

Route::get('/Picsous/categories', [App\Http\Controllers\Treso\CategorieFactController::class, 'categorie'])->name('Picsous.categorie');

Route::get('/Picsous/categories/create',[App\Http\Controllers\Treso\CategorieFactController::class, 'create'])->name('Picsous.categorie.create');
Route::post('/Picsous/categories/store',[App\Http\Controllers\Treso\CategorieFactController::class, 'store'])->name('Picsous.categorie.store');

Route::get('/Picsous/categories/edit/{categoriesFact}',[App\Http\Controllers\Treso\CategorieFactController::class, 'edit'])->name('Picsous.categorie.edit');
Route::put('/Picsous/categories/update/{categoriesFact}',[App\Http\Controllers\Treso\CategorieFactController::class, 'update'])->name('Picsous.categorie.update');
Route::delete('/Picsous/categories/{categoriesFact}',[App\Http\Controllers\Treso\CategorieFactController::class, 'destroy'])->name('Picsous.categorie.destroy');

Route::get('/Picsous/notedefrais', [App\Http\Controllers\Treso\NoteDeFraisController::class, 'notedefrais'])->name('Picsous.notedefrais');
Route::get('/Picsous/notedefrais/info/{factureRecue}', [App\Http\Controllers\Treso\NoteDeFraisController::class, 'notedefraisInfo'])->name('Picsous.notedefrais.notedefraisInfo');

Route::get('/Picsous/notedefrais/create',[App\Http\Controllers\Treso\NoteDeFraisController::class, 'create'])->name('Picsous.notedefrais.create');
Route::post('/Picsous/notedefrais/store',[App\Http\Controllers\Treso\NoteDeFraisController::class, 'store'])->name('Picsous.notedefrais.store');

Route::get('/Picsous/notedefrais/edit/{factureRecue}',[App\Http\Controllers\Treso\NoteDeFraisController::class, 'edit'])->name('Picsous.notedefrais.edit');
Route::put('/Picsous/notedefrais/update/{factureRecue}',[App\Http\Controllers\Treso\NoteDeFraisController::class, 'update'])->name('Picsous.notedefrais.update');
Route::delete('/Picsous/notedefrais/{factureRecue}',[App\Http\Controllers\Treso\NoteDeFraisController::class, 'destroy'])->name('Picsous.notedefrais.destroy');
