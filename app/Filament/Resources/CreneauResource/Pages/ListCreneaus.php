<?php

namespace App\Filament\Resources\CreneauResource\Pages;

use App\Filament\Resources\CreneauResource;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListCreneaus extends ListRecords
{
    protected static string $resource = CreneauResource::class;

    protected static function getSemester($date)
    {
        if (($date->month >= 8 && $date->month <= 12) || ($date->month >= 1 && $date->month <= 1)) {
            return 'automne';
        } elseif ($date->month >= 2 && $date->month <= 7) {
            return 'printemps';
        }
        return 'inconnu';
    }

    protected static function getStartSemester()
    {
        $currentDate = now(); // Obtenez la date actuelle
        $currentYear = Carbon::now()->year;
        $semesterStart = null; // Définir des valeurs par défaut

// Déterminez si la date actuelle est dans le semestre d'automne ou de printemps
        $currentSemester = self::getSemester($currentDate);
        if ($currentSemester === 'automne') {
            $semesterStart = Carbon::createFromDate($currentYear, 8, 15);  // 15 août
        } elseif ($currentSemester === 'printemps') {
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
        $currentSemester = self::getSemester($currentDate);
        if ($currentSemester === 'automne') {
            $semesterEnd = Carbon::createFromDate($currentYear, 1, 30);
            $semesterEnd->addYear();// 30 janvier
        }
        elseif ($currentSemester === 'printemps') {
            $semesterEnd = Carbon::createFromDate($currentYear, 7, 10);    // 10 juillet
        }
        return $semesterEnd;
    }
    public function getTabs(): array
    {
        return [
            'all' => Tab::make('tous les créneaux'),
            'libre' => Tab::make('créneaux libres')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('perm_id', null)),
        ];
    }
    protected function getHeaderActions(): array
    {
        return [
        ];
    }
    public function getHeaderWidgetsColumns(): int | array
    {
        return 5;
    }


}
