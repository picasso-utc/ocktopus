<?php

namespace App\Filament\Treso\Resources\CategorieFactureResource\Pages;

use App\Filament\Treso\Resources\CategorieFactureResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCategorieFacture extends EditRecord
{
    protected static string $resource = CategorieFactureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
