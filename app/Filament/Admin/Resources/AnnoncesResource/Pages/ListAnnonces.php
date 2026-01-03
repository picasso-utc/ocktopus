<?php

namespace App\Filament\Admin\Resources\AnnoncesResource\Pages;

use App\Filament\Admin\Resources\AnnoncesResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions;
use Illuminate\Support\Facades\File;

class ListAnnonces extends ListRecords
{
    protected static string $resource = AnnoncesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
