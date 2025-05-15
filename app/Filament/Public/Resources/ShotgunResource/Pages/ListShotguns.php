<?php

namespace App\Filament\Public\Resources\ShotgunResource\Pages;

use App\Filament\Public\Resources\ShotgunResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListShotguns extends ListRecords
{
    protected static string $resource = ShotgunResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Shotgun un event')
        ];
    }
}
