<?php

namespace App\Filament\Treso\Resources\ChequeResource\Pages;

use App\Filament\Treso\Resources\ChequeResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageCheques extends ManageRecords
{
    protected static string $resource = ChequeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
