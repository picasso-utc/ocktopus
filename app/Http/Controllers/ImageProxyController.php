<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ImageProxyController extends Controller
{
    public function compress(Request $request)
    {
        $imageUrl = $request->query('url');

        if (!$imageUrl) {
            return response()->json(['error' => 'URL manquante'], 400);
        }

        try {
            $response = Http::withHeaders(['Authorization' => 'Bearer ' . env('API_TOKEN'),])->get($imageUrl);

            if (!$response->successful()) {
                return response()->json(['error' => "Impossible de récupérer l'image"], 500);
            }

            $manager = new ImageManager(new Driver());
            $image = $manager->read($response->body());
            $encoded = $image->encodeByMediaType('image/jpeg', quality: 20);

            return response($encoded->toString(), 200)->header('Content-Type', 'image/jpeg');

        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur compression image'], 500);
        }
    }
}

