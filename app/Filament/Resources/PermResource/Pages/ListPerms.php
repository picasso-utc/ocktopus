<?php

namespace App\Filament\Resources\PermResource\Pages;

use App\Filament\Resources\PermResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPerms extends ListRecords
{
    protected static string $resource = PermResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
