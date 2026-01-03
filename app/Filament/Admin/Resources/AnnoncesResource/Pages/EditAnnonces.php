<?php

namespace App\Filament\Admin\Resources\AnnoncesResource\Pages;

use App\Filament\Admin\Resources\AnnoncesResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAnnonces extends EditRecord
{
    protected static string $resource = AnnoncesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
