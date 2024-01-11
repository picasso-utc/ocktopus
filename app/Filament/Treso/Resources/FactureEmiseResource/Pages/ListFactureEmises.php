<?php

namespace App\Filament\Treso\Resources\FactureEmiseResource\Pages;

use App\Filament\Treso\Resources\FactureEmiseResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFactureEmises extends ListRecords
{
    protected static string $resource = FactureEmiseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
