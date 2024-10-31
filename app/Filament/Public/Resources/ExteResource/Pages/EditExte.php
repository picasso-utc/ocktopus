<?php

namespace App\Filament\Public\Resources\ExteResource\Pages;

use App\Filament\Public\Resources\ExteResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditExte extends EditRecord
{
    protected static string $resource = ExteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
