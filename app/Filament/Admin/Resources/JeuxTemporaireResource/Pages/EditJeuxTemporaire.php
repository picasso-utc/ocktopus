<?php

namespace App\Filament\Admin\Resources\JeuxTemporaireResource\Pages;

use App\Filament\Admin\Resources\JeuxTemporaireResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditJeuxTemporaire extends EditRecord
{
    protected static string $resource = JeuxTemporaireResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
