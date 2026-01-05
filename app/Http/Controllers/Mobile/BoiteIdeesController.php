<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\BoiteIdees;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BoiteIdeesController extends Controller
{
    /**
     * Store a new idea from the mobile application.
     *
     * @param  Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $user = $request->get('user');

        $validator = Validator::make($request->all(), [
            'titre' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $idea = BoiteIdees::create([
                'author' => mailToName($user['email']),
                'titre' => $request->titre,
                'description' => $request->description,
                'readed' => false,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Votre idée a été enregistrée avec succès. Merci !',
                'data' => $idea
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'enregistrement de l\'idée : ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all ideas.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            $ideas = BoiteIdees::orderBy('created_at', 'desc')->get();

            return response()->json([
                'success' => true,
                'data' => $ideas
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des idées : ' . $e->getMessage()
            ], 500);
        }
    }
}
