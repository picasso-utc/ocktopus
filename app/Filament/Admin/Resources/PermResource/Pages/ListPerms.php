<?php

namespace App\Filament\Admin\Resources\PermResource\Pages;

use App\Filament\Admin\Resources\PermResource;
use App\Models\Semestre;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

/**
 * List records page for PermResource.
 */
class ListPerms extends ListRecords
{
    /**
     * The associated resource class for this page.
     *
     * @var string
     */
    protected static string $resource = PermResource::class;

    /**
     * Get the header actions for the page.
     *
     * @return array
     */
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->label('Nouvelle perm')
        ];
    }

    /**
     * Get the tabs for filtering records.
     *
     * @return array
     */
    public function getTabs(): array
    {
        return [
            // Élément pour les éléments en attente
            'waiting' => Tab::make('En attente')
                ->modifyQueryUsing(
                    function (Builder $query) {
                        // Obtient l'ID du semestre actif
                        $semestreActifId = Semestre::where('activated', true)->value('id');
                        // Modifie la requête pour avoir les perms du semestre actif et non validées
                        return $query->where('validated', 0)
                            ->where('semestre', $semestreActifId);
                    }
                ),

            // Élément pour les éléments validés
            'validated' => Tab::make('Validées')
                ->modifyQueryUsing(
                    function (Builder $query) {
                        // Obtient l'ID du semestre actif
                        $semestreActifId = Semestre::where('activated', true)->value('id');
                        // Modifie la requête pour avoir les perms du semestre actif et validées
                        return $query->where('validated', 1)
                            ->where('semestre', $semestreActifId);
                    }
                ),

            // Élément pour tous les éléments
            'all' => Tab::make('Toutes')
                ->modifyQueryUsing(
                    function (Builder $query) {
                        // Obtient l'ID du semestre actif
                        $semestreActifId = Semestre::where('activated', true)->value('id');
                        // Modifie la requête pour avoir les perms du semestre actif
                        return $query->where('semestre', $semestreActifId);
                    }
                ),
        ];
    }
}
