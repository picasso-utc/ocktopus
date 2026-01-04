<?php

namespace App\Filament\Admin\Resources\BoiteIdeesResource\Pages;

use App\Filament\Admin\Resources\BoiteIdeesResource;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\File;

class ListBoiteIdees extends ListRecords
{
    protected static string $resource = BoiteIdeesResource::class;

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
            'notread' => Tab::make('Non lues')
                ->modifyQueryUsing(
                    function (Builder $query) {
                        return $query->where('readed', false);
                    }
                ),

            // Élément pour les éléments validés
            'read' => Tab::make('Lues')
                ->modifyQueryUsing(
                    function (Builder $query) {
                        return $query->where('readed', true);
                    }
                ),
        ];
    }
}
