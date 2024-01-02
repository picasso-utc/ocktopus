<?php

namespace App\Filament\Resources\AstreinteResource\Pages;

use App\Filament\Resources\AstreinteResource;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ListAstreintes extends ListRecords
{
    protected static string $resource = AstreinteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'perso' => Tab::make('Vos notes')
                ->modifyQueryUsing(function (Builder $query) {
                    $query->where('member_id', '=', Auth::user()->id); // A modifier
                })
        ];
    }
}
