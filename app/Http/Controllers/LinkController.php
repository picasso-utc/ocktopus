<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Link;
use Illuminate\Http\Request;

class LinkController extends Controller
{
    public function links()
    {
        /*
        * Retourne une vue avec tous les liens, afin de pouvoir les visualiser, les éditer ou encore les supprimer
        */
        $links = Link::all();
        return view('TV.links.links', compact('links'));
    }

    public function create(){
        /*
         * Retourne la vue qui est un formulaire pour la création d'un lien
         */
        return view('TV.links.create' );
    }

    public function store(Request $request)
    {
        /*
         * Enregistre la création d'un média à partir d'une requête recue
         */
        $request->validate([
            'name' => 'required|string|max:50',
            'url' => 'required|string|max:300'
        ]);
        $link = new Link([
            'name' => $request->name,
            'url' => $request->url,
        ]);
        $link->save();
        return to_route('TV.links');
    }

    public function edit(Link $link){
        /*
         * retourne la vue pour modifier le lien
         */
        return view('TV.links.edit', [
                'link' => $link
            ]
        );
    }

    public function update(Request $request,Link $link){
        /*
        * Modifie le lien à partir d'une requête recue
        */
        $link->update([
            'name'=> $request->name,
            'url'=> $request->url,
        ]);
        return to_route('TV.links');
    }

    public function destroy(Request $request,Link $link){
        /*
         * Détruit le lien
         */
        $link->delete();
        return to_route('TV.links');
    }
}

