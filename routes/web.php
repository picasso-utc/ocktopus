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
Route::get('/logout',[\App\Http\Controllers\Connexion::class,'logout'])->name('logout_route');
Route::get('/userinfo', [\App\Http\Controllers\UserInfoController::class, 'getUserInfo']);


Route::get('/perm', [\App\Http\Controllers\PermController::class, 'index'])->name('perm.index');
Route::get('/perm/perms', [\App\Http\Controllers\PermController::class, 'perms'])->name('perm.perms');
Route::get('/perm/requested', [\App\Http\Controllers\PermController::class, 'requested'])->name('perm.requested');

Route::get('/perm/create', [\App\Http\Controllers\PermController::class, 'create'])->name('perm.create');
Route::post('/perm/store', [\App\Http\Controllers\PermController::class, 'store'])->name('perm.store');
Route::delete('/perm/delete/{perm}',[\App\Http\Controllers\PermController::class, 'destroy'])->name('perm.destroy');
Route::post('/perm/validate/{perm}', [\App\Http\Controllers\PermController::class, 'validatePerm'])->name('perm.validate');



Route::get('/creneau/select-dates', [\App\Http\Controllers\CreneauController::class, 'dateSelection'])->name('creneau.selectDates');
Route::post('/creneau/create-creneaux', [\App\Http\Controllers\CreneauController::class, 'createCreneaux'])->name('creneau.createCreneaux');
Route::get('creneau/liste-creneaux', [\App\Http\Controllers\CreneauController::class, 'listeCreneaux'])->name('creneau.listeCreneaux');
Route::get('creneau/liste-creneaux-semester', [\App\Http\Controllers\CreneauController::class, 'listeCreneauSemestre'])->name('creneau.listeCreneauxSemester');


Route::post('creneau/associatePerm/{creneau}', [\App\Http\Controllers\CreneauController::class, 'associatePerm'])->name('creneau.associate-perm');

Route::get('creneau/select-semestre', [\App\Http\Controllers\CreneauController::class, 'semesterSelection'])->name('creneau.select-semestre');
Route::post('creneau/create-creneaux-for-semester', [\App\Http\Controllers\CreneauController::class, 'createCreneauxForSemestre'])->name('creneau.create-creneaux-for-semester');

Route::get('/get-goodies-winner', [\App\Http\Controllers\GoodiesController::class, 'getWinner'])->name('payutc.goodiesWinner');

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

Route::get('/{record}/pdf/download', [App\Http\Controllers\DownloadPdfController::class, 'download '])->name('notedefrais.pdf.download');
