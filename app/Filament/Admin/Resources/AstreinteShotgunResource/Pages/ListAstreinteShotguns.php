<?php

namespace App\Filament\Admin\Resources\AstreinteShotgunResource\Pages;

use App\Filament\Admin\Resources\AstreinteShotgunResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAstreinteShotguns extends ListRecords
{
    protected static string $resource = AstreinteShotgunResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }
}
