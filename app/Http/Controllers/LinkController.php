<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Link;
use Illuminate\Http\Request;

class LinkController extends Controller
{
    /**
     * Retourne une vue avec tous les liens, afin de pouvoir les visualiser, les éditer ou encore les supprimer.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function links()
    {
        $links = Link::all();
        return view('TV.links.links', compact('links'));
    }

    /**
     * Retourne la vue qui est un formulaire pour la création d'un lien.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function create()
    {
        return view('TV.links.create');
    }

    /**
     * Enregistre la création d'un média à partir d'une requête reçue.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate(
            [
            'name' => 'required|string|max:50',
            'url' => 'required|string|max:300'
            ]
        );

        $link = new Link(
            [
            'name' => $request->name,
            'url' => $request->url,
            ]
        );

        $link->save();
        return to_route('TV.links');
    }

    /**
     * Retourne la vue pour modifier le lien.
     *
     * @param  \App\Models\Link $link
     * @return \Illuminate\Contracts\View\View
     */
    public function edit(Link $link)
    {
        return view(
            'TV.links.edit', [
            'link' => $link
            ]
        );
    }

    /**
     * Modifie le lien à partir d'une requête reçue.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Link         $link
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Link $link)
    {
        $link->update(
            [
            'name' => $request->name,
            'url' => $request->url,
            ]
        );
        return to_route('TV.links');
    }

    /**
     * Détruit le lien.
     *
     * @param  \App\Models\Link $link
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Link $link)
    {
        $link->delete();
        return to_route('TV.links');
    }
}
