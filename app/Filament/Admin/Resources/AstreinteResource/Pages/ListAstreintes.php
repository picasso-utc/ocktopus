<?php

namespace App\Filament\Admin\Resources\AstreinteResource\Pages;

use App\Filament\Admin\Resources\AstreinteResource;
use App\Models\Semestre;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

/**
 * List records page for AstreinteResource.
 */
class ListAstreintes extends ListRecords
{
    /**
     * The associated resource class for this page.
     *
     * @var string
     */
    protected static string $resource = AstreinteResource::class;

    /**
     * Get the header actions for the page.
     *
     * @return array
     */
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
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
            'persoNonNoté' => Tab::make('En attente de notation')
                ->modifyQueryUsing(
                    function (Builder $query) {
                        $query->where('member_id', 1)//Filament::auth()->id()
                            ->whereNull('note_orga')
                            ->whereHas(
                                'creneau', function ($query) {
                                    $query->whereNotNull('perm_id')
                                        ->whereHas(
                                            'perm', function ($query) {
                                                $semestreActifId = Semestre::where('activated', true)->value('id');
                                                $query->where('semestre', $semestreActifId);
                                            }
                                        );
                                }
                            );
                    }
                ),
            'perso' => Tab::make('Vos notes')
                ->modifyQueryUsing(
                    function (Builder $query) {
                        $query->where('member_id', 1)//Filament::auth()->id()
                            ->whereHas(
                                'creneau', function ($query) {
                                    $query->whereNotNull('perm_id')
                                        ->whereHas(
                                            'perm', function ($query) {
                                                $semestreActifId = Semestre::where('activated', true)->value('id');
                                                $query->where('semestre', $semestreActifId);
                                            }
                                        );
                                }
                            );
                    }
                ),
        ];
    }
}
