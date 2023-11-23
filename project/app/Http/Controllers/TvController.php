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
        return view('TV.index');
    }
    public function tvs()
    {
        $tvs = Tv::all();
        return view('TV.tvs', compact('tvs'));
    }
    public function show(Tv $tv){
        $link = Link::where('id', $tv->link_id)->get();
        return view('TV.display', [
                'link' => $link
            ]
        );
    }

    public function edit(Request $request, Tv $tv){
        $links = Link::all();
        return view('TV.edit', [
                'tv' => $tv,
                'links'=> $links
            ]
        );
    }

   public function update(Request $request, Tv $tv){
       $request->validate([
           'selected_link' => 'required|integer',
       ]);

       $tv->update([
            'link_id' => $request->selected_link,
        ]);
       return to_route('TV.tvs');
   }
}

