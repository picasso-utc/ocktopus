<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Events;
use App\Models\Shotgun;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ShotgunController extends Controller
{
    /**
     * Get all shotgun events with status for the current user.
     *
     * @param  Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $user = $request->get('user');
        $email = $user['email'];

        try {
            $events = Events::withCount('shotguns')
                ->orderBy('ouverture', 'desc')
                ->get();

            $formatted = $events->map(function ($event) use ($email) {
                $hasShotgunned = $event->shotguns()->where('email', $email)->exists();
                $now = Carbon::now();
                $ouverture = Carbon::parse($event->ouverture);
                
                $isOpen = $now->greaterThanOrEqualTo($ouverture);
                $isFull = $event->shotguns_count >= $event->nombre_places;

                return [
                    'id' => $event->id,
                    'titre' => $event->titre,
                    'ouverture' => $event->ouverture,
                    'debut_event' => $event->debut_event,
                    'fin_event' => $event->fin_event,
                    'nombre_places' => $event->nombre_places,
                    'categorie' => $event->categorie,
                    'shotguns_count' => $event->shotguns_count,
                    'has_shotgunned' => $hasShotgunned,
                    'is_open' => $isOpen,
                    'is_full' => $isFull,
                    'can_shotgun' => $isOpen && !$isFull && !$hasShotgunned,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $formatted
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des évènements : ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle shotgun registration for an event.
     *
     * @param  Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggle(Request $request)
    {
        $user = $request->get('user');
        $email = $user['email'];
        $eventId = $request->input('event_id');

        if (!$eventId) {
            return response()->json([
                'success' => false,
                'message' => 'ID de l\'évènement manquant.'
            ], 400);
        }

        try {
            $event = Events::withCount('shotguns')->find($eventId);

            if (!$event) {
                return response()->json([
                    'success' => false,
                    'message' => 'Évènement introuvable.'
                ], 404);
            }

            $existing = Shotgun::where('events_id', $eventId)
                ->where('email', $email)
                ->first();

            if ($existing) {
                // Unregister
                $existing->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'Désinscription réussie.',
                    'action' => 'unregistered'
                ]);
            } else {
                // Register
                $now = Carbon::now();
                $ouverture = Carbon::parse($event->ouverture);

                if ($now->lessThan($ouverture)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Le shotgun n\'est pas encore ouvert.'
                    ], 400);
                }

                if ($event->shotguns_count >= $event->nombre_places) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Plus de places disponibles.'
                    ], 400);
                }

                Shotgun::create([
                    'email' => $email,
                    'events_id' => $eventId,
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Inscription réussie !',
                    'action' => 'registered'
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'action sur le shotgun : ' . $e->getMessage()
            ], 500);
        }
    }
}
