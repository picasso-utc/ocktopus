<?php

namespace App\Filament\Admin\Resources\ElosResource\Pages;

use App\Filament\Admin\Resources\ElosResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditElos extends EditRecord
{
    protected static string $resource = ElosResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }
}
