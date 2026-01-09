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
            ->take(25)
            ->get();

        return response()->json($rankings);
    }

    public function searchUser(Request $request)
    {
        $input = $request->input('input');
        $type = $request->input('type');

        $userMails = ClassementElo::where('mail_user','LIKE','%'.$input.'%')->orWhere('nom_user','LIKE','%'.$input.'%')
            ->where('type', $type)
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
        return response()->json($elo);
    }

    public function getMarchHistory(Request $request){
        $user = $request->input('user');
        $type = $request->input('type');
        $history = HistoriqueMatch::where(function($query) use ($user, $type) {
                $query->where('mail_envoyeur', $user['email'])
                      ->orWhere('mail_receveur', $user['email']);
            })
            ->where('type', $type)
            ->orderBy('created_at', 'desc')
            ->paginate(25);
        return response()->json($history);
    }

    public function createMatchRecord(Request $request){
        $user = $request->get('user');

        $validator = Validator::make($request->all(), [
            'mail_receveur' => 'required|email',
            'type' => 'required|in:babyfoot,billard',
            'gagner' => 'required|boolean',
            'score_envoyeur' => 'nullable|integer',
            'score_receveur' => 'nullable|integer',
        ]);

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

        $currentRequests = HistoriqueMatch::whereOr('mail_envoyeur', $user['email'])
            ->whereOr('mail_receveur', $user['email'])
            ->whereOr('mail_envoyeur', $request->input('mail_receveur'))
            ->whereOr('mail_receveur', $request->input('mail_receveur'))
            ->where('type', $request->input('type'))
            ->where('valider', false)
            ->count();
        if ($currentRequests >= 1) {
            return response()->json([
                'success' => false,
                'message' => 'Une demande de match est déjà en attente pour un de ces utilisateurs'
            ], 409);
        }

        $match = HistoriqueMatch::create([
            'mail_envoyeur' => $user['email'],
            'nom_envoyeur' => mailToName($user['email']),
            'mail_receveur' => $request->input('mail_receveur'),
            'nom_receveur' => mailToName($request->input('mail_receveur')),
            'type' => $request->input('type'),
            'gagner' => $request->input('gagner'),
            'score_envoyeur' => $request->input('score_envoyeur'),
            'score_receveur' => $request->input('score_receveur')
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
        if ($match->valider === 1) {
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
            ->first();
        return response()->json($requests);
    }

    private function probability($ratingA, $ratingB){
        return 1.0/(1+pow(10,($ratingA - $ratingB)/400));
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

        $olderMatches = HistoriqueMatch::where('mail_receveur', $user['email'])
            ->where('type', $match->type)
            ->where('valider', false)
            ->where('created_at', '<', $match->created_at)
            ->count();
        if ($olderMatches > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Vous devez d\'abord répondre aux anciens matchs en attente'
            ], 409);
        }

        if($accepter){
            $match->valider = $accepter;
            $match->save();

            $eloReceveur = ClassementElo::firstOrCreate(
                ['mail_user' => $match->mail_receveur, 'type' => $match->type],
                ['elo_score' => 1000]
            );
            $eloEnvoyeur = ClassementElo::firstOrCreate(
                ['mail_user' => $match->mail_envoyeur, 'type' => $match->type],
                ['elo_score' => 1000]
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
