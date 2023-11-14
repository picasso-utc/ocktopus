<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Media;
use Illuminate\Http\Request;

class MediaController extends Controller
{
    public function index()
    {
        $medias = Media::all();
        return view('TV.index', compact('medias'));
    }
}
