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
            ]
        );
    }

    public function createCreneaux($startDate, $endDate)
    {
        // Boucle à travers chaque jour entre la date de début et la date de fin
        for ($date = $startDate; $date->lte($endDate); $date->addDay()) {
            if ($date->isWeekday()) {
                // Créer un créneau pour matin/midi/soir
                $this->createCreneau($date, 'M');
                $this->createCreneau($date, 'D');
                $this->createCreneau($date, 'S');
            }
        }
    }
}
