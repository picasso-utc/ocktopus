<?php

namespace App\Filament\Public\Resources\RequestedPermsResource\Pages;

use App\Filament\Public\Resources\RequestedPermsResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateRequestedPerms extends CreateRecord
{
    protected static string $resource = RequestedPermsResource::class;
    protected static ?string $title = 'Demander une permanence';
    protected static bool $canCreateAnother = false;
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
