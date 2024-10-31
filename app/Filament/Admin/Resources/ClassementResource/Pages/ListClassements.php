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
            'high_scores_total' => Tab::make('Total')
                ->modifyQueryUsing(
                    function (Builder $query) {
                        return $query->selectRaw('users.*,
                                COALESCE((SELECT SUM(CASE astreintes.astreinte_type
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
                                INNER JOIN creneau ON astreintes.creneau_id = creneau.id
                                INNER JOIN perms ON creneau.perm_id = perms.id
                                INNER JOIN semestres ON perms.semestre_id = semestres.id
                                WHERE astreintes.user_id = users.id 
                                AND semestres.activated = 1), 0) as points')
                            ->orderBy('points', 'desc');

                    }
                ),
            'high_scores_matin' => Tab::make('Matin')
                ->modifyQueryUsing(
                    function (Builder $query) {
                        return $query->selectRaw('users.*,
                                COALESCE((SELECT SUM(CASE astreintes.astreinte_type
                                            WHEN "Matin 1" THEN 1
                                            WHEN "Matin 2" THEN 1
                                            ELSE 0
                                          END)
                                FROM astreintes
                                INNER JOIN creneau ON astreintes.creneau_id = creneau.id
                                INNER JOIN perms ON creneau.perm_id = perms.id
                                INNER JOIN semestres ON perms.semestre_id = semestres.id
                                WHERE astreintes.user_id = users.id 
                                AND semestres.activated = 1), 0) as points')
                            ->orderBy('points', 'desc');

                    }
                ),
            'high_scores_midi' => Tab::make('Midi')
                ->modifyQueryUsing(
                    function (Builder $query) {
                        return $query->selectRaw('users.*,
                                COALESCE((SELECT SUM(CASE astreintes.astreinte_type
                                            WHEN "Déjeuner 1" THEN 1
                                            WHEN "Déjeuner 2" THEN 1
                                            ELSE 0
                                          END)
                                FROM astreintes
                                INNER JOIN creneau ON astreintes.creneau_id = creneau.id
                                INNER JOIN perms ON creneau.perm_id = perms.id
                                INNER JOIN semestres ON perms.semestre_id = semestres.id
                                WHERE astreintes.user_id = users.id 
                                AND semestres.activated = 1), 0) as points')
                            ->orderBy('points', 'desc');

                    }
                ),
            'high_scores_soir' => Tab::make('Soir')
                ->modifyQueryUsing(
                    function (Builder $query) {
                        return $query->selectRaw('users.*,
                                COALESCE((SELECT SUM(CASE astreintes.astreinte_type
                                            WHEN "Soir 1" THEN 2.5
                                            WHEN "Soir 2" THEN 2
                                            ELSE 0
                                          END)
                                FROM astreintes
                                INNER JOIN creneau ON astreintes.creneau_id = creneau.id
                                INNER JOIN perms ON creneau.perm_id = perms.id
                                INNER JOIN semestres ON perms.semestre_id = semestres.id
                                WHERE astreintes.user_id = users.id 
                                AND semestres.activated = 1), 0) as points')
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
