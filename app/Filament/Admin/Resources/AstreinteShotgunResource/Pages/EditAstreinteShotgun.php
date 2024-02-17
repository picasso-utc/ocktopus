<?php

namespace App\Filament\Admin\Resources\AstreinteShotgunResource\Pages;

use App\Filament\Admin\Resources\AstreinteShotgunResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAstreinteShotgun extends EditRecord
{
    protected static string $resource = AstreinteShotgunResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }
}
