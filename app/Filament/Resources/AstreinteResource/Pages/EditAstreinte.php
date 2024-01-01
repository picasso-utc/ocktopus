<?php

namespace App\Filament\Resources\AstreinteResource\Pages;

use App\Filament\Resources\AstreinteResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAstreinte extends EditRecord
{
    protected static string $resource = AstreinteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
