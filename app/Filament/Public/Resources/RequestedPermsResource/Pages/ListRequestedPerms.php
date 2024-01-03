<?php

namespace App\Filament\Public\Resources\RequestedPermsResource\Pages;

use App\Filament\Public\Resources\RequestedPermsResource;
use App\Models\Perm;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListRequestedPerms extends ListRecords
{
    protected static string $resource = RequestedPermsResource::class;

    protected function getTableQuery(): ?Builder
    {
        return Perm::query()
            ->where('mail_resp', Filament::auth()->user()?->email);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Demander une permanence'),
        ];
    }
}
