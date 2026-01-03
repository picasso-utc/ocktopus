<?php

namespace App\Filament\Admin\Resources\SemesterEventResource\Pages;

use App\Filament\Admin\Resources\SemesterEventResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSemesterEvent extends EditRecord
{
    protected static string $resource = SemesterEventResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
