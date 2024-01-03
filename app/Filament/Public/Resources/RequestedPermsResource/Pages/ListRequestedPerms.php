<?php

namespace App\Filament\Public\Resources\RequestedPermsResource\Pages;

use App\Filament\Public\Resources\RequestedPermsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRequestedPerms extends ListRecords
{
    protected static string $resource = RequestedPermsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Demander une permanence'),
        ];
    }
}
