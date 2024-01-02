<?php

namespace App\Filament\Admin\Resources\PermResource\Pages;

use App\Filament\Admin\Resources\PermResource;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListPerms extends ListRecords
{
    protected static string $resource = PermResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
    public function getTabs(): array
    {
        return [
            'waiting' => Tab::make('En attente')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('validated', 0)),
            'validated' => Tab::make('Validées')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('validated', 1)),
            'all' => Tab::make('Toutes'),
        ];
    }
}
