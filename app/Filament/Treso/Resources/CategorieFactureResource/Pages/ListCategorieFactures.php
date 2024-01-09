<?php

namespace App\Filament\Treso\Resources\CategorieFactureResource\Pages;

use App\Filament\Treso\Resources\CategorieFactureResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCategorieFactures extends ListRecords
{
    protected static string $resource = CategorieFactureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
