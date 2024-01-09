<?php

namespace App\Filament\Treso\Resources\FactureRecueResource\Pages;

use App\Filament\Treso\Resources\FactureRecueResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateFactureRecue extends CreateRecord
{
    protected static string $resource = FactureRecueResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
