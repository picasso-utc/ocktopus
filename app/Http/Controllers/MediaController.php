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

}
