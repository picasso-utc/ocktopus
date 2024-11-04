<?php

namespace App\Filament\Public\Resources\ExteResource\Pages;

use App\Filament\Public\Resources\ExteResource;
use App\Models\Exte;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListExtes extends ListRecords
{
    protected static string $resource = ExteResource::class;

    protected function getTableQuery(): ?Builder
    {
        return Exte::query()
            ->where('etu_mail', Filament::auth()->user()?->email);
    }
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Demander un exté')
        ];
    }
}
