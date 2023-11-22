<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Media;
use GuzzleHttp\Psr7\UploadedFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class MediaController extends Controller
{
    public function content()
    {
        // Sélectionnez les médias où activate est égal à 1
        $medias = Media::where('activate', 1)->get();
        return view('TV.content', compact('medias'));
    }

    public function medias()
    {
        $medias = Media::all();
        return view('TV.medias.medias', compact('medias'));
    }


    public function create(){
        return view('TV.medias.create' );
    }

    public function edit(Request $request, Media $media){
        return view('TV.medias.edit', [
            'media' => $media
            ]
        );
    }


    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:50',
            'media_type' => 'required|in:Image,Video',
            'activate' => 'boolean',
            'times' => 'required|integer',
            'media_path' => 'required|file|mimes:jpeg,png,mp4', // Ajoutez les types de fichiers acceptés
    //        'duree' => 'required_if:media_type,Video|integer', // La durée est requise uniquement pour les vidéos
        ]);

        $media = new Media([
            'name' => $request->name,
            'media_type' => $request->media_type,
            'activate' => $request->has('activate'),
            'times' => $request->times,
        ]);

        //méthode pour store le média et avoir pour l'attribut média_path, le chemin d'accès
        $mediaPath = $request->file('media_path')->store('TV', 'public');
        $media->media_path = $mediaPath;

        $media->save();

        // met à jour la durée si le média est de type "Vidéo"
        //possible de faire autrement je pense
        if ($request->media_type === 'Video') {
            $media->update(['duree' => $request->duree]);
        }

        return to_route('TV.medias');

        }
    public function update(Request $request, Media $media)
    {
        // Si un nouveau fichier média est fourni
        if ($request->hasFile('media_path')) {
            // Stockez le nouveau fichier média
            $newMediaPath = $request->file('media_path')->store('TV', 'public');
            if ($media->media_path != null){
                Storage::disk('public')->delete($media->media_path);
            }
            $media->update([
                'media_path' => $newMediaPath,
            ]);
            if ($media->media_type === 'Video') { //si on a changé de média et que l'on a affaire une vidéo
                $media->update(['duree' => $request->duree]);
            }
        }
        $media->update([
            'name'=>$request->name,
            'activate'=>$request->has('activate'),
            'times'=>$request->times,
        ]);

        return to_route('TV.medias');
    }

    public function destroy(Media $media){
        if ($media->media_path != null){
            Storage::disk('public')->delete($media->media_path);
        }
        $media->delete();

        return to_route('TV.medias');
    }
};


