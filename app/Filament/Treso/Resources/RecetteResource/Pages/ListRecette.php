<?php

namespace App\Filament\Treso\Resources\RecetteResource\Pages;

use App\Filament\Treso\Resources\RecetteResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRecette extends ListRecords
{
    protected static string $resource = RecetteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
