<?php

namespace App\Filament\Admin\Resources\SemestreResource\Pages;

use App\Filament\Admin\Resources\SemestreResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSemestres extends ListRecords
{
    protected static string $resource = SemestreResource::class;

    protected function handleCreateSemestre()
    {
        $startDate=now()->addYear(2);
        $endDate=now()->addYear(2)->addMonth(6);
        $creneauController = new CreneauController();
        $creneauController->createCreneaux($startDate, $endDate);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make('createSemestre')
                ->label('Créer un Semestre'),

                     ];
    }
}
