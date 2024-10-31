<?php

namespace App\Filament\Admin\Resources\ExteResource\Pages;

use App\Filament\Admin\Resources\ExteResource;
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
