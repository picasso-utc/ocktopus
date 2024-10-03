<?php

namespace App\Filament\Admin\Resources\ClassementResource\Pages;

use App\Filament\Admin\Resources\ClassementResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Filament\Resources\Components\Tab;


class ListClassements extends ListRecords
{
    protected static string $resource = ClassementResource::class;


    public function getTabs(): array
    {
        return [
            'high_scores' => Tab::make('Les best    ')
                ->modifyQueryUsing(
                    function (Builder $query) {
                        return $query->selectRaw('users.*,
                                (SELECT SUM(CASE astreintes.astreinte_type
                                            WHEN "Matin 1" THEN 1
                                            WHEN "Matin 2" THEN 1
                                            WHEN "Déjeuner 1" THEN 1
                                            WHEN "Déjeuner 2" THEN 1
                                            WHEN "Autre" THEN 1
                                            WHEN "Soir 1" THEN 2.5
                                            WHEN "Soir 2" THEN 2
                                            ELSE 0
                                          END)
                                FROM astreintes
                                WHERE astreintes.user_id = users.id) as points')
                            ->orderBy('points', 'desc');
                    }
                ),
        ];
    }


    protected function getHeaderActions(): array
    {
        return [
        ];
    }
}
