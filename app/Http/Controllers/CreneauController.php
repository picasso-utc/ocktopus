<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Perm;
use Illuminate\Http\Request;
use App\Models\Creneau;
use Carbon\Carbon;



class CreneauController extends Controller
{

    public function dateSelection()
    {
        return view('creneau.selectDates');
    }

    public function semesterSelection()
    {
        return view('creneau.selectSemester');
    }



    private function createCreneau(Carbon $date, string $creneau)
    {
        Creneau::create([
            'date' => $date,
            'creneau' => $creneau,
            // Autres colonnes et valeurs nécessaires
        ]);
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

        redirect(route('creneau.listeCreneaux'));
    }
    public function createCreneauxForSemestre(Request $request)
    {

        $semestre = $request->input('semestre');
        $currentYear = Carbon::now()->year;
        // Définir les dates de début et de fin du semestre
        if ($semestre === 'automne') {
            $semesterStart = Carbon::createFromDate($currentYear, 8, 15);  // 15 août
            $semesterEnd = Carbon::createFromDate($currentYear, 1, 30);
            $semesterEnd->addYear();// 30 janvier
        } elseif ($semestre === 'printemps') {
            $semesterStart = Carbon::createFromDate($currentYear, 2, 1);   // 1er février
            $semesterEnd = Carbon::createFromDate($currentYear, 7, 10);    // 10 juillet
        } else {
            // Gérer le cas où le semestre n'est pas défini correctement
            return redirect()->back()->with('error', 'Semestre non valide');
        }

        // Boucle à travers chaque jour entre la date de début et la date de fin du semestre
        $this->createCreneaux($semesterStart,$semesterEnd);

        return redirect(route('creneau.listeCreneaux'));
    }


    public function listeCreneaux($startDate, $endDate)
    {

        // Récupérer les créneaux entre la date de début et la date de fin
        $creneaux = Creneau::whereBetween('date', [$startDate, $endDate])->get();
        $perms = Perm::all();
        return view('creneau.listeCreneaux', ['creneaux' => $creneaux, 'perms'=>$perms]);
    }

    public function listeCreneauSemestre()
    {
        $currentDate = now(); // Obtenez la date actuelle
        $currentYear = Carbon::now()->year;

        // Déterminez si la date actuelle est dans le semestre d'automne ou de printemps
        $currentSemester = $this->getSemester($currentDate);
        if ($currentSemester === 'automne') {
            $semesterStart = Carbon::createFromDate($currentYear, 8, 15);  // 15 août
            $semesterEnd = Carbon::createFromDate($currentYear, 1, 30);
            $semesterEnd->addYear();// 30 janvier
        } elseif ($currentSemester === 'printemps') {
            $semesterStart = Carbon::createFromDate($currentYear, 2, 1);   // 1er février
            $semesterEnd = Carbon::createFromDate($currentYear, 7, 10);    // 10 juillet
        } else {
            // Gérer le cas où le semestre n'est pas défini correctement
            return redirect()->back()->with('error', 'Semestre non valide');
        }

        // Passez les données à votre vue
        return $this->listeCreneaux($semesterStart,$semesterEnd);
    }


    /**
     * Détermine le semestre pour une date donnée.
     *
     * @param  \Illuminate\Support\Carbon  $date
     * @return string
     */
    private function getSemester($date)
    {
        if (($date->month >= 8 && $date->month <= 12) || ($date->month >= 1 && $date->month <= 1)) {
            return 'automne';
        } elseif ($date->month >= 2 && $date->month <= 7) {
            return 'printemps';
        }

        return 'inconnu';
    }

    public function associatePerm(Request $request, Creneau $creneau)
    {
        // Validez la requête
        $request->validate([
            'perm_id' => 'required|exists:perms,id',
        ]);

        // Associez la perm au créneau
        $creneau->perm_id = $request->input('perm_id');
        $creneau->save();

        return redirect(route('creneau.listeCreneaux')); //améliorer pour pas avoir à refaire à chaque fois
    }


}
