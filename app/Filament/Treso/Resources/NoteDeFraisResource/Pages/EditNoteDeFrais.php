<?php

namespace App\Filament\Treso\Resources\NoteDeFraisResource\Pages;

use App\Filament\Treso\Resources\NoteDeFraisResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditNoteDeFrais extends EditRecord
{
    protected static string $resource = NoteDeFraisResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
