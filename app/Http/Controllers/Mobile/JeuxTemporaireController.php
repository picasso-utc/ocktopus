<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\JeuxTemporaire;
use Illuminate\Http\Request;

class JeuxTemporaireController extends Controller
{
    /**
     * Get all temporary games.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            $jeux = JeuxTemporaire::orderBy('created_at', 'desc')->get();

            return response()->json([
                'success' => true,
                'data' => $jeux
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des jeux temporaires : ' . $e->getMessage()
            ], 500);
        }
    }
}
