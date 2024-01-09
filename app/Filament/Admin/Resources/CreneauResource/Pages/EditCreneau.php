<?php

namespace App\Filament\Admin\Resources\CreneauResource\Pages;

use App\Filament\Admin\Resources\CreneauResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCreneau extends EditRecord
{
    protected static string $resource = CreneauResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
