<?php

namespace App\Filament\Public\Resources\ExteResource\Pages;

use App\Filament\Public\Resources\ExteResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateExte extends CreateRecord
{
    protected static ?string $title = 'Demande d\'exté';
    protected static bool $canCreateAnother = false;
    protected static string $resource = ExteResource::class;
    protected function getCreateFormAction(): \Filament\Actions\Action
    {
        return parent::getCreateFormAction()
            ->label('Demander');
    }

}
