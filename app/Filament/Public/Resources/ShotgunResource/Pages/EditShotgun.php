<?php

namespace App\Filament\Public\Resources\ShotgunResource\Pages;

use App\Filament\Public\Resources\ShotgunResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditShotgun extends EditRecord
{
    protected static string $resource = ShotgunResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
