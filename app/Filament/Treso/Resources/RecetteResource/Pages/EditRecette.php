<?php

namespace App\Filament\Treso\Resources\RecetteResource\Pages;

use App\Filament\Treso\Resources\RecetteResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRecette extends EditRecord
{
    protected static string $resource = RecetteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
