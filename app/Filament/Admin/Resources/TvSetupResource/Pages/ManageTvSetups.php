<?php

namespace App\Filament\Admin\Resources\TvSetupResource\Pages;

use App\Filament\Admin\Resources\TvSetupResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageTvSetups extends ManageRecords
{
    protected static string $resource = TvSetupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
