<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Link;
use App\Models\Tv;
use Illuminate\Http\Request;

class TvController extends Controller
{

    public function index()
    {
        /*
         * Retourne la vue pour naviguer entre gestion des médias, TVs et liens
         */
        return view('TV.index');
    }
    public function tvs()
    {
        /*
         * Retourne une vue avec tous les TVs, afin de pouvoir les visualiser, les éditer
         */
        $tvs = Tv::all();
        return view('TV.tvs', compact('tvs'));
    }
    public function show(Tv $tv){
        /*
         * Retourne ce qu'affiche une TV
         */
        //on récupère le lien qu'a pour attribut la TV
        $link = Link::where('id', $tv->link_id)->get();
        return view('TV.display', [
                'link' => $link
            ]
        );
    }
    public function create(){
        /*
         * Retourne la vue qui est un formulaire pour la création d'une TV
         */
        $links = Link::all();

        return view('TV.create' ,[
            'links' => $links
        ]);
    }

    public function store(Request $request)
    {
        /*
         * Enregistre la création d'un média à partir d'une requête recue
         */
        $request->validate([
            'name' => 'required|string|max:50',
            'selected_link' => 'required|integer'
        ]);
        $tv = new tv([
            'name' => $request->name,
            'link_id' => $request->selected_link,
        ]);
        $tv->save();
        return to_route('TV.tvs');
    }
    public function edit(Tv $tv){
        /*
         * retourne la vue pour modifier la TV
         */
        $links = Link::all();
        return view('TV.edit', [
                'tv' => $tv,
                'links'=> $links
            ]
        );
    }


    public function update(Request $request, Tv $tv)
    {
        $request->validate([
            'selected_link' => 'required|integer',
        ]);

        // Utiliser la relation pour mettre à jour link_id
        $tv->link()->associate($request->selected_link)->save();

        return redirect()->route('TV.tvs');
    }

}
