<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Media;
use GuzzleHttp\Psr7\UploadedFile;
use Illuminate\Http\Request;


class MediaController extends Controller
{
    public function index()
    {
        // Sélectionnez les médias où activate est égal à 1
        $medias = Media::where('activate', 1)->get();
        return view('TV.index', compact('medias'));
    }

    public function content()
    {
        $medias = Media::all();
        return view('TV.content', compact('medias'));
    }

    public function create(){
        return view('TV.create' );
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

    return redirect()->route('TV.index');

    }
}
