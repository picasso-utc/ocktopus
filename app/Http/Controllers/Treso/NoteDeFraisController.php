<?php

namespace App\Http\Controllers\Treso;

use App\Http\Controllers\Controller;
use App\Models\Treso\NoteDeFrais;
use App\Models\Treso\ElementFacture;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class NoteDeFraisController extends Controller
{
    public function notedefrais()
    {
        $noteDeFrais = NoteDeFrais::all();
        $elementFacture = ElementFacture::all();
        return view('Picsous.notedefrais.notedefrais', compact('noteDeFrais', 'elementFacture'));
    }

    public function notedefraisInfo(FactureRecue $factureRecue)
    {
        $noteDeFrais = NoteDeFrais::all();
        $elementFacture = ElementFacture::all();
        return view('Picsous.notedefrais.notedefraisInfo', compact('noteDeFrais', 'elementFacture'));
    }

    public function create()
    {
        $noteDeFrais = NoteDeFrais::all();
        return view('Picsous.notedefrais.create', compact('noteDeFrais'));
    }

    public function store(Request $request)
    {

        $noteDeFrais = new noteDeFrais(
            [
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'state' => $request->state,
            'numero_voie' => $request->numero_voie,
            'rue' => $request->rue,
            'code_postal' => $request->code_postal,
            'ville' => $request->ville,
            'email' => $request->email,
            'date_facturation' => $request->date_facturation
            ]
        );
        $noteDeFrais->save();

        $elementFacture = new ElementFacture(
            [
            'description' => $request->description,
            'prix_unitaire_ttc' => $request->prix_unitaire_ttc,
            'tva' => $request->tva,
            'quantite' => $request->quantite,
            'note_de_frais_id' => $noteDeFrais->id
            ]
        );
        $elementFacture->save();

        return to_route('Picsous.notedefrais');
    }

    public function edit(FactureRecue $factureRecue)
    {
        $categorieFacture = CategorieFacture::all();
        return view(
            'Picsous.noteDeFrais.edit',
            [
                'factureRecue' => $factureRecue,
                'categorieFactureRecues' => $categorieFacture

            ]
        );
    }

    public function update(Request $request, FactureRecue $factureRecue)
    {

        dump($request->state);
        $request->validate(
            [
            'nom_entreprise' => 'required|string|max:255',
            'prix' => 'required|integer',
            'state' => 'required|string',
            'tva' => 'required|integer'
            ]
        );
        dump($request->state);
        //dump($request->state);
        $factureRecue->update(
            [
            'nom_entreprise' => $request->nom_entreprise,
            'prix' => $request->prix,
            'state' => $request->state,
            'tva' => $request->tva
            ]
        );
        return to_route('Picsous.noteDeFrais');
    }

    public function destroy(Request $request, FactureRecue $factureRecue)
    {
        $factureRecue->delete();
        return to_route('Picsous.noteDeFrais');
    }
}
