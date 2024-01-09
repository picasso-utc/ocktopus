<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Link;
use App\Models\Tv;
use Illuminate\Http\Request;

class TvController extends Controller
{
    /**
     * Affiche la vue pour naviguer entre la gestion des médias, TVs et liens.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        return view('TV.index');
    }

    /**
     * Affiche une vue avec tous les TVs pour visualisation et édition.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function tvs()
    {
        $tvs = Tv::all();
        return view('TV.tvs', compact('tvs'));
    }

    /**
     * Affiche ce qu'affiche une TV en récupérant le lien attribué à la TV.
     *
     * @param  Tv $tv
     * @return \Illuminate\Contracts\View\View
     */
    public function show(Tv $tv)
    {
        $link = Link::find($tv->link_id);
        return view(
            'TV.display',
            [
            'link' => $link
            ]
        );
    }

    /**
     * Affiche le formulaire pour la création d'une TV avec la liste des liens disponibles.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function create()
    {
        $links = Link::all();
        return view(
            'TV.create',
            [
            'links' => $links
            ]
        );
    }

    /**
     * Enregistre la création d'un média à partir d'une requête reçue.
     *
     * @param  Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate(
            [
            'name' => 'required|string|max:50',
            'selected_link' => 'required|integer'
            ]
        );

        $tv = new Tv(
            [
            'name' => $request->name,
            'link_id' => $request->selected_link,
            ]
        );

        $tv->save();

        return redirect()->route('TV.tvs');
    }

    /**
     * Affiche la vue pour modifier la TV avec la liste des liens disponibles.
     *
     * @param  Tv $tv
     * @return \Illuminate\Contracts\View\View
     */
    public function edit(Tv $tv)
    {
        $links = Link::all();
        return view(
            'TV.edit',
            [
            'tv' => $tv,
            'links' => $links
            ]
        );
    }

    /**
     * Met à jour la TV avec le lien sélectionné.
     *
     * @param  Request $request
     * @param  Tv      $tv
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Tv $tv)
    {
        $request->validate(
            [
            'selected_link' => 'required|integer',
            ]
        );

        $tv->link()->associate($request->selected_link)->save();

        return redirect()->route('TV.tvs');
    }
}
