<?php

namespace App\Filament\Treso\Resources\FactureRecueResource\Pages;

use App\Filament\Treso\Resources\FactureRecueResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFactureRecues extends ListRecords
{
    protected static string $resource = FactureRecueResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
