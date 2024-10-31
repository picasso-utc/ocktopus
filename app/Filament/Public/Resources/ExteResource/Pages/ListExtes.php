<?php

namespace App\Filament\Public\Resources\ExteResource\Pages;

use App\Filament\Public\Resources\ExteResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListExtes extends ListRecords
{
    protected static string $resource = ExteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Demander un exté')
        ];
    }
}
