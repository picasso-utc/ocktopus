<?php

namespace App\Filament\Treso\Resources\NoteDeFraisResource\Pages;

use App\Filament\Treso\Resources\NoteDeFraisResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListNoteDeFrais extends ListRecords
{
    protected static string $resource = NoteDeFraisResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
