<?php

namespace App\Filament\Treso\Resources\CategorieFactureResource\Pages;

use App\Filament\Treso\Resources\CategorieFactureResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Validation\ValidationException;

class CreateCategorieFacture extends CreateRecord
{
    protected static string $resource = CategorieFactureResource::class;

    protected function onValidationError(ValidationException $exception): void
    {
        Notification::make()
            ->title($exception->getMessage())
            ->danger()
            ->send();
    }
}
