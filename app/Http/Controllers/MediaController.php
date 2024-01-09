<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    /**
     * Retourne le lien par défaut "content" qui affiche les médias activés.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function content()
    {
        // Sélectionne les médias activés
        $medias = Media::where('activated', 1)->get();
        return view('TV.content', compact('medias'));
    }

    /**
     * Retourne une vue avec tous les médias, afin de pouvoir les visualiser, les éditer ou encore les supprimer.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function medias()
    {
        $medias = Media::all();
        return view('TV.medias.medias', compact('medias'));
    }

    /**
     * Retourne la vue qui est un formulaire pour la création d'un média.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function create()
    {
        return view('TV.medias.create');
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
            'media_type' => 'required|in:Image,Video',
            'activated' => 'boolean',
            'times' => 'required|integer',
            'media_path' => [
                'required',
                'file',
                'mimes:' . ($request->input('media_type') === 'Video' ? 'mp4' : 'jpeg,png'),
            ],
            ]
        );

        $media = new Media(
            [
            'name' => $request->name,
            'media_type' => $request->media_type,
            'activated' => $request->has('activate'),
            'times' => $request->times,
            ]
        );

        // Méthode pour stocker le média et avoir pour l'attribut "media_path" le chemin d'accès
        $mediaPath = $request->file('media_path')->store('TV', 'public');
        $media->media_path = $mediaPath;

        $media->save();
        return redirect()->route('TV.medias');
    }

    /**
     * Retourne la vue pour modifier le média.
     *
     * @param  \App\Models\Media $media
     * @return \Illuminate\Contracts\View\View
     */
    public function edit(Media $media)
    {
        return view(
            'TV.medias.edit',
            [
            'media' => $media
            ]
        );
    }

    /**
     * Modifie le média à partir d'une requête reçue.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Media        $media
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Media $media)
    {
        // Si un nouveau fichier média est fourni
        if ($request->hasFile('media_path')) {
            // Stockez le nouveau fichier média
            $newMediaPath = $request->file('media_path')->store('TV', 'public');
            // Et supprimer l'ancien média
            if ($media->media_path != null) {
                // On vérifie juste que l'on avait un média pour ne pas faire d'erreur
                Storage::disk('public')->delete($media->media_path);
            }
            $media->update(
                [
                'media_path' => $newMediaPath,
                ]
            );
        }

        $media->update(
            [
            'name' => $request->name,
            'activated' => $request->has('activate'),
            'times' => $request->times,
            ]
        );

        return redirect()->route('TV.medias');
    }

    /**
     * Détruit le média en supprimant d'abord le fichier correspondant.
     *
     * @param  \App\Models\Media $media
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Media $media)
    {
        // Il faut d'abord supprimer le fichier
        if ($media->media_path != null) {
            Storage::disk('public')->delete($media->media_path);
        }

        $media->delete();

        return redirect()->route('TV.medias');
    }
}
