<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Link;
use Illuminate\Http\Request;

class LinkController extends Controller
{
    public function links()
    {
        $links = Link::all();
        return view('TV.links.links', compact('links'));
    }

    public function create(){
        return view('TV.links.create' );
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:50',
            'url' => 'required|string|max:350'
        ]);
        $link = new Link([
            'name' => $request->name,
            'url' => $request->url,
        ]);
        $link->save();
        return to_route('TV.links');
    }

    public function edit(Link $link){
        return view('TV.links.edit', [
                'link' => $link
            ]
        );
    }

    public function update(Request $request,Link $link){
        $link->update([
            'name'=> $request->name,
            'url'=> $request->url,
        ]);
    return to_route('TV.links');
    }

    public function destroy(Request $request,Link $link){
            $link->delete();
        return to_route('TV.links');
    }
}


