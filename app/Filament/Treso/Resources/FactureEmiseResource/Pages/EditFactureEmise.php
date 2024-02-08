<?php

namespace App\Filament\Treso\Resources\FactureEmiseResource\Pages;

use App\Filament\Treso\Resources\FactureEmiseResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFactureEmise extends EditRecord
{
    protected static string $resource = FactureEmiseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
