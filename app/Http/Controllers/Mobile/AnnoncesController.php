<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Annonces;
use Illuminate\Http\Request;

class AnnoncesController extends Controller
{
    /**
     * Get all announcements.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            $annonces = Annonces::orderBy('mis_en_avant', 'desc')
                ->orderBy('created_at', 'desc')
                ->get();

            $formatted = $annonces->map(function ($annonce) {
                return [
                    'id' => $annonce->id,
                    'titre' => $annonce->titre,
                    'type' => $annonce->type,
                    'courte_desc' => $annonce->courte_desc,
                    'longue_desc' => $annonce->longue_desc,
                    'mis_en_avant' => $annonce->mis_en_avant,
                    'media_path' => $annonce->media_path,
                    'media_url' => $annonce->media_path ? url('/image') . '?url=' . $annonce->media_path : null,
                    'created_at' => $annonce->created_at,
                    'updated_at' => $annonce->updated_at,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $formatted
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des annonces : ' . $e->getMessage()
            ], 500);
        }
    }
}
