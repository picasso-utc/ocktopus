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

            return response()->json([
                'success' => true,
                'data' => $annonces
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des annonces : ' . $e->getMessage()
            ], 500);
        }
    }
}
