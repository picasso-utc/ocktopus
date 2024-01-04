<?php

namespace App\Filament\Admin\Resources\CreneauResource\Pages;

use App\Filament\Admin\Resources\CreneauResource;
use App\Models\Creneau;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Semestre;



class ListCreneaus extends ListRecords
{
    protected static string $resource = CreneauResource::class;

    protected static function getStateSemester(){
        $semestre = Semestre::where('activated', true)->first();

        if ($semestre) {
            return $semestre->state;
        }
        return "nope";
    }

    protected static function getStartSemester()
    {
        $semestre = Semestre::where('activated', true)->first();

        if ($semestre) {
            return $semestre->startOfSemestre;
        }

        // Si aucun semestre activé n'est trouvé, vous pouvez renvoyer une date par défaut ou gérer cela selon vos besoins.
        return now(); // Date par défaut, ajustez selon vos besoins.
    }
    protected static function getEndSemester()
    {
        $semestre = Semestre::where('activated', true)->first();

        if ($semestre) {
            return $semestre->endOfSemestre;
        }

        // Si aucun semestre activé n'est trouvé, vous pouvez renvoyer une date par défaut ou gérer cela selon vos besoins.
        return now()->addMonth(6); // Date par défaut, ajustez selon vos besoins.

    }

    protected function createCreneau(Carbon $date, string $creneau)
    {
        Creneau::create([
            'date' => $date,
            'creneau' => $creneau,
            // Autres colonnes et valeurs nécessaires
        ]);
    }
    public function createCreneaux(Carbon $startDate, Carbon $endDate)
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
        public function getTabs(): array
    {
        return [
            'semestre' => Tab::make(self::getStateSemester())
                ->modifyQueryUsing(function (Builder $query) {
                    return $query->whereBetween('date', [self::getStartSemester(), self::getEndSemester()]);
                })
        ];
    }
    protected function getHeaderActions(): array
    {
        return [
        ];
    }
}
