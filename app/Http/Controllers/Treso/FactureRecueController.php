<?php

namespace App\Http\Controllers\Treso;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ShowPdfController;
use App\Models\Treso\CategorieFacture;
use App\Models\Treso\FactureRecue;
use App\Models\Treso\MontantCategorie;
use Illuminate\Http\Request;

class FactureRecueController extends Controller
{
    public function facturerecue()
    {
        $factureRecues = FactureRecue::all();
        $montantCategorie = MontantCategorie::all();
        $categorieFacture = CategorieFacture::all();
        return view('Picsous.factureRecue.factureRecue', compact('factureRecues','categorieFacture','montantCategorie'));
    }

    public function facturerecueInfo(FactureRecue $factureRecue){
        $montantCategorie = MontantCategorie::all();
        $categorieFacture = CategorieFacture::all();
        return view('Picsous.factureRecue.facturerecueInfo', compact('factureRecue', 'montantCategorie', 'categorieFacture'));
    }

    public function create(){
        $categorieFacture = CategorieFacture::all();
        return view('Picsous.factureRecue.create', compact('categorieFacture'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom_entreprise' => 'required|string|max:255',
            'prix' => 'required|integer',
            'state' => 'required|string|max:1',
            'tva' => 'required|integer'
        ]);

        if (is_null($request->immobilisation)) {
            $request->immobilisation = false;
        }

        $factureRecue = new FactureRecue([
            'nom_entreprise' => $request->nom_entreprise,
            'prix' => $request->prix,
            'state' => $request->state,
            'tva' => $request->tva,
            'date' => $request->date,
            'date_remboursement' => $request->date_remboursement,
            'date_paiement' => $request->date_paiement,
            'moyen_paiement' => $request->moyen_paiement,
            'personne_a_rembourser' => $request->personne_a_rembourser,
            'immobilisation' => $request->immobilisation
        ]);
        $factureRecue->save();

        $categoriePrix = new MontantCategorie([
            'prix' => $request->categorie_prix,
            'categorie_id' => $request->categorie_id,
            'facture_id' => $factureRecue->id,
        ]);
        $categoriePrix->save();

        $categoriePrix2 = new MontantCategorie([
            'prix' => $request->categorie_prix2,
            'categorie_id' => $request->categorie_id2,
            'facture_id' => $factureRecue->id,
        ]);
        $categoriePrix2->save();
        return to_route('Picsous.facturerecue');
    }

    public function edit(FactureRecue $factureRecue){
        $categorieFacture = CategorieFacture::all();
        return view('Picsous.facturerecue.edit', [
                'factureRecue' => $factureRecue,
                'categorieFactureRecues' => $categorieFacture

            ]
        );
    }

    public function update(Request $request,FactureRecue $factureRecue){

        dump($request->state);
        $request->validate([
            'nom_entreprise' => 'required|string|max:255',
            'prix' => 'required|integer',
            'state' => 'required|string',
            'tva' => 'required|integer'
        ]);
        dump($request->state);
        //dump($request->state);
        $factureRecue->update([
            'nom_entreprise' => $request->nom_entreprise,
            'prix' => $request->prix,
            'state' => $request->state,
            'tva' => $request->tva
        ]);
        return to_route('Picsous.facturerecue');
    }

    public function destroy(Request $request,FactureRecue $factureRecue){
        $factureRecue->delete();
        return to_route('Picsous.facturerecue');
    }

}
