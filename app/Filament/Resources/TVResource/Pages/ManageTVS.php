<?php

namespace App\Filament\Resources\TVResource\Pages;

use App\Filament\Resources\TVResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageTVS extends ManageRecords
{
    protected static string $resource = TVResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
