<?php

namespace App\Filament\Admin\Resources\CreneauShotgunResource\Pages;

use App\Filament\Admin\Resources\CreneauShotgunResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCreneauShotguns extends ListRecords
{
    protected static string $resource = CreneauShotgunResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
