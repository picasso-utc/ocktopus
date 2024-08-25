<?php

namespace App\Filament\Admin\Resources\PermResource\Pages;

use App\Filament\Admin\Resources\PermResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePerm extends CreateRecord
{
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    protected static string $resource = PermResource::class;
    protected static ?string $title = 'Nouvelle permanence';
}
