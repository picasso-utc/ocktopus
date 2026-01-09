<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Semestre;
use App\Models\SignatureCharte;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SignatureCharteController extends Controller
{
    /**
     * Récupère le semestre courant.
     *
     */
    private function getCurrentSemestre(): ?Semestre
    {
        $today = now()->toDateString();

        $semestre = Semestre::query()
            ->where('activated', 1)
            ->orderByDesc('startOfSemestre')
            ->first();

        if ($semestre) {
            return $semestre;
        }

        $semestre = Semestre::query()
            ->whereDate('startOfSemestre', '<=', $today)
            ->whereDate('endOfSemestre', '>=', $today)
            ->orderByDesc('startOfSemestre')
            ->first();

        if ($semestre) {
            return $semestre;
        }

        return Semestre::query()
            ->orderByDesc('startOfSemestre')
            ->first();
    }

    /**
     * GET — Indique si l'utilisateur a signé la charte pour le semestre courant.
     */
    public function hasSigned(Request $request)
    {
        $user = $request->input('user') ?? $request->get('user');

        if (!is_array($user) || empty($user['email'])) {
            return response()->json([
                'success' => false,
                'message' => 'Utilisateur non authentifié',
            ], 401);
        }

        $semestre = $this->getCurrentSemestre();
        if (!$semestre) {
            return response()->json([
                'success' => false,
                'message' => 'Aucun semestre trouvé',
            ], 404);
        }

        $exists = SignatureCharte::query()
            ->where('adresse_mail', $user['email'])
            ->where('semestre_id', $semestre->id)
            ->exists();

        return response()->json([
            'success' => true,
            'data' => [
                'signed' => $exists,
                'semestre' => [
                    'id' => $semestre->id,
                    'state' => $semestre->state ?? null,
                    'startOfSemestre' => $semestre->startOfSemestre ?? null,
                    'endOfSemestre' => $semestre->endOfSemestre ?? null,
                    'activated' => $semestre->activated ?? null,
                ],
            ],
        ], 200);
    }

    /**
     * POST — Enregistre la signature de l'utilisateur pour le semestre courant.
     *
     */
    public function sign(Request $request)
    {
        $user = $request->input('user') ?? $request->get('user');

        if (!is_array($user) || empty($user['email'])) {
            return response()->json([
                'success' => false,
                'message' => 'Utilisateur non authentifié',
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'agree' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Requête invalide',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Si agree est présent, on peut exiger true
        if ($request->has('agree') && !$request->boolean('agree')) {
            return response()->json([
                'success' => false,
                'message' => 'Vous devez accepter la charte pour signer',
            ], 422);
        }

        $semestre = $this->getCurrentSemestre();
        if (!$semestre) {
            return response()->json([
                'success' => false,
                'message' => 'Aucun semestre trouvé',
            ], 404);
        }

        // Unique (adresse_mail, semestre_id) => firstOrCreate évite les doublons.
        $signature = SignatureCharte::query()->firstOrCreate([
            'adresse_mail' => $user['email'],
            'semestre_id' => $semestre->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Charte signée',
            'data' => [
                'id' => $signature->id,
                'adresse_mail' => $signature->adresse_mail,
                'semestre_id' => $signature->semestre_id,
                'created_at' => $signature->created_at,
            ],
        ], 201);
    }
}
