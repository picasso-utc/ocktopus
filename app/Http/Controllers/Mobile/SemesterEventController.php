<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\SemesterEvent;
use Illuminate\Http\Request;

class SemesterEventController extends Controller
{
    /**
     * Get all semester events.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            $events = SemesterEvent::orderBy('date', 'asc')->get();

            return response()->json([
                'success' => true,
                'data' => $events
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des évènements du semestre : ' . $e->getMessage()
            ], 500);
        }
    }
}
