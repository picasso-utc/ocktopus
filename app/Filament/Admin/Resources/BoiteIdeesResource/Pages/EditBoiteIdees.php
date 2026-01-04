<?php

namespace App\Filament\Admin\Resources\BoiteIdeesResource\Pages;

use App\Filament\Admin\Resources\BoiteIdeesResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBoiteIdees extends EditRecord
{
    protected static string $resource = BoiteIdeesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
