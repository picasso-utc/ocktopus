<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Creneau;
use Carbon\Carbon;



class CreneauController extends Controller
{

    public function dateSelection()
    {
        return view('creneau.selectDates');
    }
    private function createCreneau(Carbon $date, string $creneau)
    {
        Creneau::create([
            'date' => $date,
            'creneau' => $creneau,
            // Autres colonnes et valeurs nécessaires
        ]);
    }

    public function createCreneaux(Request $request)
    {
        $startDate = Carbon::parse($request->input('start_date'));
        $endDate = Carbon::parse($request->input('end_date'));

        // Boucle à travers chaque jour entre la date de début et la date de fin
        for ($date = $startDate; $date->lte($endDate); $date->addDay()) {
            if ($date->isWeekday()) {
                // Créer un créneau pour le matin
                $this->createCreneau($date, 'M');
                // Créer un créneau pour le déjeuner
                $this->createCreneau($date, 'D');
                // Créer un créneau pour le soir
                $this->createCreneau($date, 'S');
            }
        }

        redirect(route('creneau.listeCreneaux'));
    }
    public function listeCreneauxForm()
    {
        return view('creneau.listeCreneaux');
    }

    public function listeCreneaux(Request $request)
    {
        $startDate = Carbon::parse($request->input('start_date'));
        $endDate = Carbon::parse($request->input('end_date'));

        // Récupérer les créneaux entre la date de début et la date de fin
        $creneaux = Creneau::whereBetween('date', [$startDate, $endDate])->get();

        return view('creneau.listeCreneaux', ['creneaux' => $creneaux]);
    }
}
