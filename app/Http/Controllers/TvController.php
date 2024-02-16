<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Link;
use App\Models\Tv;
use Illuminate\Http\Request;

class TvController extends Controller
{


    /**
     * Affiche ce qu'affiche une TV en récupérant le lien attribué à la TV.
     *
     * @param Tv $tv
     * @return \Illuminate\Contracts\View\View
     */
    public function show(Tv $tv)
    {
        $link = Link::find($tv->link_id);
        return view('TV.display', [
            'link' => $link
        ]);
    }

}
