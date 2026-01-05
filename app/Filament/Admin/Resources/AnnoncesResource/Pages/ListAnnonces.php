<?php

namespace App\Filament\Admin\Resources\AnnoncesResource\Pages;

use App\Filament\Admin\Resources\AnnoncesResource;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions;
use Illuminate\Support\Facades\File;
use Illuminate\Database\Eloquent\Builder;

class ListAnnonces extends ListRecords
{
    protected static string $resource = AnnoncesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            // Élément pour tous les éléments
            'all' => Tab::make('Toutes')
                ->modifyQueryUsing(
                    function (Builder $query) {
                        return $query;
                    }
                ),

            // Élément pour les éléments en attente
            'active' => Tab::make('Actives')
                ->modifyQueryUsing(
                    function (Builder $query) {
                        return $query->where('is_active', true);
                    }
                ),

            // Élément pour les éléments validés
            'not_active' => Tab::make('Inactives')
                ->modifyQueryUsing(
                    function (Builder $query) {
                        return $query->where('is_active', false);
                    }
                ),
        ];
    }
}
