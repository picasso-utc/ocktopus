<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Perm;
use App\Models\Semestre;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PermController extends Controller
{
    /**
     * Store a new permanence request from a mobile user.
     */
    public function store(Request $request)
    {
        $user = $request->get('user');

        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255',
            'theme' => 'required|string|max:255',
            'description' => 'required|string',
            'teddy' => 'required|boolean',
            'repas' => 'required|boolean',
            'idea_repas' => 'nullable|string|max:255',
            'nom_resp_2' => 'required|string|max:255',
            'mail_resp_2' => 'required|email|max:255',
            'asso' => 'required|boolean',
            'mail_asso' => 'required_if:asso,true|nullable|email|max:255',
            'ambiance' => 'required|integer|min:1|max:5',
            'periode' => 'required|string|max:255',
            'jour' => 'required|array',
            'membres' => 'required|array',
            'artiste' => 'required|boolean',
            'remarques' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $semestreActif = Semestre::where('activated', true)->first();

        if (!$semestreActif) {
            return response()->json([
                'success' => false,
                'message' => 'Aucun semestre actif trouvé.'
            ], 400);
        }

        try {
            $perm = Perm::create([
                'nom' => $request->nom,
                'theme' => $request->theme,
                'description' => $request->description,
                'teddy' => $request->teddy,
                'repas' => $request->repas,
                'idea_repas' => $request->idea_repas,
                'nom_resp' => mailToName($user['email']),
                'mail_resp' => $user['email'],
                'nom_resp_2' => $request->nom_resp_2,
                'mail_resp_2' => $request->mail_resp_2,
                'asso' => $request->asso,
                'mail_asso' => $request->mail_asso,
                'ambiance' => $request->ambiance,
                'periode' => $request->periode,
                'jour' => $request->jour,
                'membres' => implode(' - ', $request->membres),
                'artiste' => $request->artiste,
                'remarques' => $request->remarques,
                'semestre_id' => $semestreActif->id,
                'validated' => false,
            ]);

            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('perms', 'public');
                $perm->update(['image_path' => $path]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Demande de permanence enregistrée avec succès.',
                'data' => $perm
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'enregistrement de la permanence : ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get the permanences for the current or next week.
     * Shows next week's perms once Saturday arrives.
     */
    public function currentWeek()
    {
        $now = Carbon::now();
        $isSaturdayOrAfter = $now->dayOfWeek === Carbon::SATURDAY
            || $now->dayOfWeek === Carbon::SUNDAY;

        if ($isSaturdayOrAfter) {
            $start = $now->copy()->next(Carbon::MONDAY)->toDateString();
            $end = $now->copy()->next(Carbon::SATURDAY)->toDateString();
            $weekLabel = 'next_week';
        } else {
            $start = $now->copy()->startOfWeek(Carbon::MONDAY)->toDateString();
            $end = $now->copy()->endOfWeek(Carbon::FRIDAY)->toDateString();
            $weekLabel = 'current_week';
        }

        try {
            $creneaux = \App\Models\Creneau::with('perm')
                ->whereBetween('date', [$start, $end])
                ->whereNotNull('perm_id')
                ->orderBy('date')
                ->orderByRaw("FIELD(creneau, 'M', 'D', 'S', 'L')")
                ->get();

            $formatted = $creneaux->map(function ($creneau) {
                return [
                    'id' => $creneau->id,
                    'date' => $creneau->date,
                    'creneau_type' => $creneau->creneau,
                    'perm_id' => $creneau->perm_id,
                    'perm_nom' => $creneau->perm->nom,
                    'theme' => $creneau->perm->theme,
                    'description' => $creneau->perm->description,
                    'ambiance' => $creneau->perm->ambiance,
                    'nom_resp' => $creneau->perm->nom_resp,
                    'mail_resp' => $creneau->perm->mail_resp,
                    'nom_resp_2' => $creneau->perm->nom_resp_2,
                    'mail_resp_2' => $creneau->perm->mail_resp_2,
                    'asso' => $creneau->perm->asso,
                    'mail_asso' => $creneau->perm->mail_asso,
                    'membres' => $creneau->perm->membres,
                    'teddy' => $creneau->perm->teddy,
                    'repas' => $creneau->perm->repas,
                    'idea_repas' => $creneau->perm->idea_repas,
                    'remarques' => $creneau->perm->remarques,
                    'artiste' => $creneau->perm->artiste,
                    'image_url' => $creneau->perm->image_path
                        ? url('storage/' . $creneau->perm->image_path)
                        : null,
                ];
            });

            return response()->json([
                'success' => true,
                'week_label' => $weekLabel,
                'data' => $formatted
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des permanences : ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get permanence requests for the authenticated user.
     */
    public function index(Request $request)
    {
        try {
            $user = $request->get('user');
            $perms = Perm::where('mail_resp', $user['email'])
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $perms
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des demandes de permanence : ' . $e->getMessage()
            ], 500);
        }
    }
}
