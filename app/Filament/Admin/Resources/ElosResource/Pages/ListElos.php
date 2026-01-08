<?php

namespace App\Filament\Admin\Resources\ElosResource\Pages;

use App\Filament\Admin\Resources\ElosResource;
use Illuminate\Database\Eloquent\Builder;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;

class ListElos extends ListRecords
{
    protected static string $resource = ElosResource::class;

    public function getTabs(): array
    {

        return [

            'baby_foot' => Tab::make('Baby Foot')
                ->modifyQueryUsing(
                    function (Builder $query) {
                        return $query->where('type', 'babyfoot')
                            ->orderBy('elo_score', 'desc');
                    }
                ),

            'billard' => Tab::make('Billard')
                ->modifyQueryUsing(
                    function (Builder $query) {
                        return $query->where('type', 'billard')
                            ->orderBy('elo_score', 'desc');
                    }
                )
        ];
    }


    protected function getHeaderActions(): array
    {
        return [
        ];
    }
}
