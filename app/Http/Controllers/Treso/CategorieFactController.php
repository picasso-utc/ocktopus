<?php

namespace App\Http\Controllers\Treso;

use App\Http\Controllers\Controller;
use App\Models\Treso\CategorieFacture;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CategorieFactController extends Controller
{
    public function categorie()
    {
        $categorieFacture = CategorieFacture::all();
        return view('Picsous.categorie.factureRecue', compact('categorieFacture'));
    }

    public function create()
    {
        return view('Picsous.categorie.create');
    }

    public function store(Request $request)
    {
        $request->validate(
            [
            'nom' => 'required|string|max:255',
            'code' => [
                'required',
                'string',
                'max:1',
                Rule::unique('categorie_factures'),
            ]
            ]
        );

        $categorieFacture = new CategorieFacture(
            [
            'nom' => $request->nom,
            'code' => $request->code
            ]
        );

        $categorieFacture->save();
        return to_route('Picsous.facturerecue.create');
    }

    public function edit(CategorieFacture $categorieFactureRecues)
    {
        return view(
            'Picsous.categorie.edit',
            [
                'categorieFactureRecues' => $categorieFactureRecues
            ]
        );
    }

    public function update(Request $request, CategorieFacture $categorieFactureRecues)
    {
        $categorieFactureRecues->update(
            [
            'nom' => $request->nom,
            'code' => $request->code,
            ]
        );
        return to_route('Picsous.facturerecue');
    }

    public function destroy(Request $request, CategorieFacture $categorieFactureRecues)
    {
        $categorieFactureRecues->delete();
        return to_route('Picsous.facturerecue');
    }
}
