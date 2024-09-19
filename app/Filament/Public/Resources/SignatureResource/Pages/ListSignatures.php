<?php

namespace App\Filament\Public\Resources\SignatureResource\Pages;

use App\Filament\Public\Resources\SignatureResource;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListSignatures extends ListRecords
{
    protected static string $resource = SignatureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Signer la charte')
                    ->createAnother(false),
        ];
    }

    public function getTabs(): array
    {
        // Filtrer les permanences en fonction de l'email de l'utilisateur connecté
        return [
            'signatures'=> Tab::make('Mes signatures')
                ->modifyQueryUsing(function (Builder $query) {
                    $query->where('adresse_mail', session('user')->email);
                }),
        ];
    }
}
