<?php

namespace App\Filament\Resources\AstreinteResource\Pages;

use App\Filament\Resources\AstreinteResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAstreintes extends ListRecords
{
    protected static string $resource = AstreinteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
