<?php

namespace App\Filament\Public\Resources\RequestedPermsResource\Pages;

use App\Filament\Public\Resources\RequestedPermsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRequestedPerms extends EditRecord
{
    protected static string $resource = RequestedPermsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
