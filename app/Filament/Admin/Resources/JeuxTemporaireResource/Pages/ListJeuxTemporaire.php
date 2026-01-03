<?php

namespace App\Filament\Admin\Resources\JeuxTemporaireResource\Pages;

use App\Filament\Admin\Resources\JeuxTemporaireResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions;

class ListJeuxTemporaire extends ListRecords
{
    protected static string $resource = JeuxTemporaireResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
