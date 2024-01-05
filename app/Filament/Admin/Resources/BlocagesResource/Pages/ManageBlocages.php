<?php

namespace App\Filament\Admin\Resources\BlocagesResource\Pages;

use App\Filament\Admin\Resources\BlocagesResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageBlocages extends ManageRecords
{
    protected static string $resource = BlocagesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
