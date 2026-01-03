<?php

namespace App\Filament\Admin\Resources\SemesterEventResource\Pages;

use App\Filament\Admin\Resources\SemesterEventResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions;

class ListSemesterEvent extends ListRecords
{
    protected static string $resource = SemesterEventResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
