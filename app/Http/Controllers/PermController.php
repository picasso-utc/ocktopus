<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Perm;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PermController extends Controller
{
    /**
     * Affiche la liste des perms.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index() : View
    {
        return view('perm.index');
    }

    public function perms() : View
    {
        $perms = Perm::all();
        return view('perm.perms', compact('perms'));
    }


    /**
     * Affiche le formulaire de création d'une perm.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function create()
    {
        return view('perms.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'theme' => 'required|string|max:255',
            'description' => 'string|max:500',
            'periode' => 'string|max:500',
            'membres' => 'string|max:500',
            //'asso' => 'boolean',
            'nom_resp' => 'required|string|max:255',
            'nom_resp2' => 'required|string|max:255',
            'mail_resp' => 'required|email|max:255',
            'mail_resp2' => 'required|email|max:255',
            'mail_asso' => 'email|max:255',
            'ambiance' => 'required|integer|min:1|max:5',

            // Ajoutez d'autres règles de validation selon vos besoins
        ]);

        Perm::create($request->all());

        return redirect()->route('perm.perms')->with('success', 'La perm a été créée avec succès.');
    }

    /**
     * Supprime une perm spécifique.
     *
     * @param  \App\Models\Perm  $perm
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Perm $perm)
    {
        $perm->delete();
        return redirect()->route('perm.perms')->with('success', 'La perm a été supprimée avec succès.');
    }

}

