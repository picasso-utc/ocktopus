<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ClassementElo;
use App\Models\HistoriqueMatch;
use Illuminate\Support\Facades\Validator;

class Elo extends Controller
{
    public function getRankings(Request $request)
    {
        $type = $request->input('type');

        $rankings = ClassementElo::where('type', $type)
            ->orderBy('elo_score', 'desc')
            ->take(10)
            ->get()
            ->map(function ($ranking) use ($type) {
                $result = $ranking->toArray();
                $result['elo_delta_week'] = $this->eloDeltaWeek($ranking->mail_user, $type);

                return $result;
            });

        return response()->json($rankings);
    }

    public function searchUser(Request $request)
    {
        $input = $request->input('input');
        $type = $request->input('type');

        $userMails = ClassementElo::query()
            ->where('type', $type)
            ->where(function ($q) use ($input) {
                $q->where('mail_user', 'LIKE', '%' . $input . '%')
                    ->orWhere('nom_user', 'LIKE', '%' . $input . '%');
            })
            ->take(10)
            ->get();

        return response()->json($userMails);
    }

    public function getUserElo(Request $request){
        $user = $request->input('user');
        $type = $request->input('type');
        $elo = ClassementElo::firstOrCreate(
            ['mail_user' => $user['email'], 'type' => $type],
            ['elo_score' => 1000, 'nom_user' => mailToName($user['email'])]
        );

        $rank = ClassementElo::where('type', $type)
            ->where('elo_score', '>', $elo->elo_score)
            ->count() + 1;

        $result = $elo->toArray();
        $result['rank'] = $rank;
        $result['elo_delta_week'] = $this->eloDeltaWeek($user['email'], $type);

        return response()->json($result);
    }

    public function getMarchHistory(Request $request){
        $user = $request->input('user');
        $type = $request->input('type');
        $history = HistoriqueMatch::where(function($query) use ($user, $type) {
                $query->where('mail_envoyeur', $user['email'])
                      ->orWhere('mail_receveur', $user['email']);
            })
            ->where('type', $type)
            ->where('valider', true)
            ->orderBy('created_at', 'desc')
            ->paginate(25);
        return response()->json($history);
    }

    public function createMatchRecord(Request $request){
        $user = $request->get('user');
        $type = $request->input('type');

        $rules = [
            'mail_receveur' => 'required|email',
            'type' => 'required|in:babyfoot,billard',
        ];
        if ($type === 'babyfoot') {
            $rules['score_envoyeur'] = 'required|integer|min:0';
            $rules['score_receveur'] = 'required|integer|min:0|different:score_envoyeur';
        } else {
            $rules['gagner'] = 'required|boolean';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Il manque des informations',
                'errors' => $validator->errors()
            ], 422);
        }

        if (!isset($user['email'])) {
            return response()->json([
                'success' => false,
                'message' => 'Utilisateur non authentifié'
            ], 401);
        }

        $isBabyfoot = $type === 'babyfoot';
        $gagner = $isBabyfoot
            ? $request->input('score_envoyeur') > $request->input('score_receveur')
            : $request->boolean('gagner');

        $match = HistoriqueMatch::create([
            'mail_envoyeur' => $user['email'],
            'nom_envoyeur' => mailToName($user['email']),
            'mail_receveur' => $request->input('mail_receveur'),
            'nom_receveur' => mailToName($request->input('mail_receveur')),
            'type' => $type,
            'gagner' => $gagner,
            'score_envoyeur' => $isBabyfoot ? $request->input('score_envoyeur') : null,
            'score_receveur' => $isBabyfoot ? $request->input('score_receveur') : null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Match enregistré avec succès',
            'data' => $match
        ], 201);
    }

    public function cancelMatchRecord(Request $request){
        $user = $request->input('user');
        $matchId = $request->input('match_id');

        $match = HistoriqueMatch::find($matchId);
        if (!$match) {
            return response()->json([
                'success' => false,
                'message' => 'Match non trouvé'
            ], 404);
        }
        if ($match->mail_envoyeur !== $user['email']) {
            return response()->json([
                'success' => false,
                'message' => 'Accès refusé pour ce match'
            ], 403);
        }
        if ($match->valider) {
            return response()->json([
                'success' => false,
                'message' => 'Ce match à déjà été accepté, il ne peut plus être annulé'
            ], 418); //418 = I'm a teapot (ça fait 5 ans je veux utilisé ce code erreur laissez le moi svp)
        }

        $match->delete();

        return response()->json([
            'success' => true,
            'message' => 'Match annulé avec succès',
            'data' => $match
        ], 200);
    }

    public function getMatchRequests(Request $request){
        $user = $request->input('user');
        $type = $request->input('type');
        $requests = HistoriqueMatch::where('mail_receveur', $user['email'])
            ->where('type', $type)
            ->where('valider', false)
            ->orderBy('created_at', 'asc')
            ->get();
        return response()->json($requests);
    }

    private function probability($ratingA, $ratingB){
        return 1.0/(1+pow(10,($ratingA - $ratingB)/400));
    }

    private function eloDeltaWeek(string $email, string $type): int
    {
        $since = now()->subDays(7);

        $asEnvoyeur = HistoriqueMatch::where('mail_envoyeur', $email)
            ->where('type', $type)
            ->where('valider', true)
            ->where('updated_at', '>=', $since)
            ->sum('elo_delta_envoyeur');

        $asReceveur = HistoriqueMatch::where('mail_receveur', $email)
            ->where('type', $type)
            ->where('valider', true)
            ->where('updated_at', '>=', $since)
            ->sum('elo_delta_receveur');

        return (int) ($asEnvoyeur + $asReceveur);
    }

    public function respondMatch(Request $request){
        $user = $request->input('user');
        $matchId = $request->input('match_id');
        $accepter = $request->input('accepter');
        $match = HistoriqueMatch::find($matchId);
        if (!$match || $match->mail_receveur !== $user['email']) {
            return response()->json([
                'success' => false,
                'message' => 'Match non trouvé ou accès refusé'
            ], 404);
        }

        if($accepter){
            $match->valider = $accepter;
            $match->save();

            $eloReceveur = ClassementElo::firstOrCreate(
                ['mail_user' => $match->mail_receveur, 'type' => $match->type],
                ['elo_score' => 1000, 'nom_user' => mailToName($match->mail_receveur)]
            );
            $eloEnvoyeur = ClassementElo::firstOrCreate(
                ['mail_user' => $match->mail_envoyeur, 'type' => $match->type],
                ['elo_score' => 1000, 'nom_user' => mailToName($match->mail_envoyeur)]
            );

            $K = 32;

            $probabilityEnvoyeur = $this->probability($eloReceveur->elo_score, $eloEnvoyeur->elo_score);
            $probabilityReceveur = $this->probability($eloEnvoyeur->elo_score, $eloReceveur->elo_score);

            if($match->gagner){
                $newEloEnvoyeur = $eloEnvoyeur->elo_score + $K * (1 - $probabilityEnvoyeur);
                $newEloReceveur = $eloReceveur->elo_score + $K * (0 - $probabilityReceveur);
            } else {
                $newEloEnvoyeur = $eloEnvoyeur->elo_score + $K * (0 - $probabilityEnvoyeur);
                $newEloReceveur = $eloReceveur->elo_score + $K * (1 - $probabilityReceveur);
            }

            $match->elo_delta_envoyeur = round($newEloEnvoyeur) - $eloEnvoyeur->elo_score;
            $match->elo_delta_receveur = round($newEloReceveur) - $eloReceveur->elo_score;
            $match->save();

            $eloEnvoyeur->elo_score = round($newEloEnvoyeur);
            $eloReceveur->elo_score = round($newEloReceveur);
            $eloEnvoyeur->save();
            $eloReceveur->save();
        } else {
            $match->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Réponse au match enregistrée avec succès',
            'data' => $match
        ], 200);
    }
}
