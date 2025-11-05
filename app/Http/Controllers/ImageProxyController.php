<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\JpegEncoder;

class ImageProxyController extends Controller
{
    public function compress(Request $request)
    {
        $imageUrl = $request->query('url');

        if (!$imageUrl) {
            return response()->json(['error' => 'URL manquante'], 400);
        }

        try {
            $imageUid = Str::after($imageUrl, 'private/');
            $filePath = 'public/imagesBornes/' . $imageUid;

            if (Storage::exists($filePath)) {
                return response()->file(Storage::path($filePath));
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('API_TOKEN'),
            ])->get($imageUrl);

            if (!$response->successful()) {
                return response()->json(['error' => "Impossible de récupérer l'image"], 500);
            }

            $manager = new ImageManager(new Driver());
            $image = $manager->read($response->body());
            $encoded = $image->encode(new JpegEncoder(quality: 1));

            Storage::put($filePath, $encoded);
            Storage::setVisibility($filePath, 'public');

            return response($encoded->toString(), 200)->header('Content-Type', 'image/jpeg');
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erreur compression image',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
