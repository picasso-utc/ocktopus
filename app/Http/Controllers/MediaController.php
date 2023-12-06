<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class MediaController extends Controller
{

    public function content()
    {
        /*
         * Retourne le lien par défault content qui affiche les méidas activés
         */
        // Selectionne les médias activés
        $medias = Media::where('activated', 1)->get();
        return view('TV.content', compact('medias'));
    }

    public function medias()
    {
        /*
         * Retourne une vue avec tous les médias, afin de pouvoir les visualiser, les éditer ou encore les supprimer
         */
        $medias = Media::all();
        return view('TV.medias.medias', compact('medias'));
    }


    public function create(){
        /*
         * Retourne la vue qui est un formulaire pour la création d'un média
         */
        return view('TV.medias.create' );
    }

    public function store(Request $request)
    {
        /*
         * Enregistre la création d'un média à partir d'une requête recue
         */
        $request->validate([
            'name' => 'required|string|max:50',
            'media_type' => 'required|in:Image,Video',
            'activated' => 'boolean',
            'times' => 'required|integer',
            'media_path' => [
                'required',
                'file',
                'mimes:' . ($request->input('media_type') === 'Video' ? 'mp4' : 'jpeg,png'),
            ],
        ]);

        $media = new Media([
            'name' => $request->name,
            'media_type' => $request->media_type,
            'activated' => $request->has('activate'),
            'times' => $request->times,
        ]);

        //méthode pour store le média et avoir pour l'attribut média_path, le chemin d'accès
        $mediaPath = $request->file('media_path')->store('TV', 'public');
        $media->media_path = $mediaPath;



        $media->save();
        return to_route('TV.medias');

    }

    public function edit(Media $media){
        /*
         * retourne la vue pour modifier le média
         */
        return view('TV.medias.edit', [
                'media' => $media
            ]
        );
    }



    public function update(Request $request, Media $media)
    {
        /*
        * Modifie le média à partir d'une requête recue
        */
        // Si un nouveau fichier média est fourni
        if ($request->hasFile('media_path')) {
            // Stockez le nouveau fichier média
            $newMediaPath = $request->file('media_path')->store('TV', 'public');
            //Et supprimer l'ancien média
            if ($media->media_path != null){ //on vérifie juste que l'on avait un média pour pas faire d'erreur
                Storage::disk('public')->delete($media->media_path);
            }
            $media->update([
                'media_path' => $newMediaPath,
            ]);

        }
        $media->update([
            'name'=>$request->name,
            'activated'=>$request->has('activate'),
            'times'=>$request->times,
        ]);

        return to_route('TV.medias');
    }

    public function destroy(Media $media){
        /*
         * Détruit le média
         */
        //Il faut d'abord supprimer le fichier
        if ($media->media_path != null){
            Storage::disk('public')->delete($media->media_path);
        }
        $media->delete();

        return to_route('TV.medias');
    }
};

