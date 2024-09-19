<?php

namespace App\Filament\Public\Resources\SignatureResource\Pages;

use App\Filament\Public\Resources\SignatureResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateSignature extends CreateRecord
{
    protected static string $resource = SignatureResource::class;
    protected static ?string $title = 'Charte du Permanancier';
    protected static bool $canCreateAnother = false;

    protected function getCreateFormAction(): \Filament\Actions\Action
    {
        return parent::getCreateFormAction()
            ->label('Signer');
    }

}
