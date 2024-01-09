<?php

namespace App\Filament\Admin\Resources\CreneauResource\Pages;

use App\Filament\Admin\Resources\CreneauResource;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Semestre;
use Illuminate\Support\Carbon;

class ListCreneaus extends ListRecords
{
    protected static string $resource = CreneauResource::class;

    /**
     * Get the state of the active semester.
     *
     * @return string
     */
    protected static function getStateSemester(): string
    {
        $semestre = Semestre::where('activated', true)->first();

        return $semestre ? $semestre->state : 'nope';
    }

    /**
     * Get the start date of the active semester.
     *
     * @return Carbon
     */
    protected static function getStartSemester(): string
    {
        $semestre = Semestre::where('activated', true)->first();

        return $semestre ? $semestre->startOfSemestre : now();
    }

    /**
     * Get the end date of the active semester.
     *
     * @return mixed
     */
    protected static function getEndSemester(): mixed //string ou carbon
    {
        $semestre = Semestre::where('activated', true)->first();

        return $semestre ? $semestre->endOfSemestre : now()->addMonth();
    }

    /**
     * Get the tabs for the resource page.
     *
     * @return array
     */
    public function getTabs(): array
    {
        return [
            'semestre' => Tab::make(self::getStateSemester())
                ->modifyQueryUsing(
                    function (Builder $query) {
                        // Les créneaux qui se situent entre le début et la fin du semestre actif
                        return $query->whereBetween('date', [self::getStartSemester(), self::getEndSemester()]);
                    }
                ),
        ];
    }

    /**
     * Get the header actions for the resource page.
     *
     * @return array
     */
    protected function getHeaderActions(): array
    {
        return [
        ];
    }
}
