<?php

namespace App\Filament\Admin\Resources\PermResource\Pages;

use App\Filament\Admin\Resources\PermResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPerm extends ViewRecord
{
    protected static string $resource = PermResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
