<?php

namespace App\Filament\Admin\Resources\TVLinkResource\Pages;

use App\Filament\Admin\Resources\TVLinkResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageTVLinks extends ManageRecords
{
    protected static string $resource = TVLinkResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->label('Nouveau lien'),
        ];
    }
}
