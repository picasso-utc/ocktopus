<?php

namespace App\Filament\Resources\CreneauResource\Pages;

use App\Filament\Resources\CreneauResource;
use App\Models\Creneau;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;



class ListCreneaus extends ListRecords
{
    protected static string $resource = CreneauResource::class;

    protected static function getStartSemester()
    {
        $currentDate = now(); // Obtenez la date actuelle
        $currentYear = Carbon::now()->year;
        $semesterStart = null; // Définir des valeurs par défaut

// Déterminez si la date actuelle est dans le semestre d'automne ou de printemps
        if ($currentDate->month >= 7 && $currentDate->day>=15) {
            $semesterStart = Carbon::createFromDate($currentYear, 8, 15);  // 15 août
        }
        elseif ($currentDate->month >= 1 && $currentDate->day <= 20) {
            $semesterStart = Carbon::createFromDate($currentYear, 8, 15);
            $semesterStart->subYear();//retirer une aneee
        }// 15 août
        else {
            $semesterStart =  Carbon::createFromDate($currentYear, 2, 1);   // 1er février
        }
        return $semesterStart;
    }
    protected static function getEndSemester()
    {
        $currentDate = now(); // Obtenez la date actuelle
        $currentYear = Carbon::now()->year;
        $semesterEnd = null; // Définir des valeurs par défaut

// Déterminez si la date actuelle est dans le semestre d'automne ou de printemps
        if ($currentDate->month >= 7 && $currentDate->day>=15) {
            $semesterEnd = Carbon::createFromDate($currentYear, 1, 30);
            $semesterEnd->addYear();// 30 janvier
        }
        elseif ($currentDate->month >= 1 && $currentDate->day <= 20){
            $semesterEnd = Carbon::createFromDate($currentYear, 1, 30);
        }
        else  {
            $semesterEnd = Carbon::createFromDate($currentYear, 7, 10);    // 10 juillet
        }
        return $semesterEnd;
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
            'semestre' => Tab::make('Planning')
                ->modifyQueryUsing(fn (Builder $query) => $query->when(
                    now()->between(self::getStartSemester(), self::getEndSemester()),
                    fn (Builder $query): Builder => $query
                        ->whereDate('date', '>=', self::getStartSemester())
                        ->whereDate('date', '<=', self::getEndSemester()),
                )
                )
        ];
    }
    protected function getHeaderActions(): array
    {
        return [
//            Actions\CreateAction::make('createCreneaux')
//                ->label('Créer Créneaux')
//                ->render(fn () => $this->createCreneaux(now(), now()->addDays(7))),
        ];
    }
    public function getHeaderWidgetsColumns(): int | array
    {
        return 5;
    }


}
