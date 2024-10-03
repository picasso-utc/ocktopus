<?php

namespace App\Filament\Admin\Resources\ClassementResource\Pages;

use App\Filament\Admin\Resources\ClassementResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditClassement extends EditRecord
{
    protected static string $resource = ClassementResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }
}
