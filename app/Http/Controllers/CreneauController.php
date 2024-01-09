<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Perm;
use Illuminate\Http\Request;
use App\Models\Creneau;
use Carbon\Carbon;



class CreneauController extends Controller
{


    private function createCreneau(Carbon $date, string $creneau)
    {
        Creneau::create(
            [
            'date' => $date,
            'creneau' => $creneau,
            // Autres colonnes et valeurs nécessaires
            ]
        );
    }

    public function createCreneaux($startDate, $endDate)
    {
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
    }

}
