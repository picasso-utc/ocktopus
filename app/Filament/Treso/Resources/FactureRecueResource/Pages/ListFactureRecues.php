<?php

namespace App\Filament\Treso\Resources\FactureRecueResource\Pages;

use App\Filament\Treso\Resources\FactureRecueResource;
use App\Models\FactureRecue;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Forms\Components\DatePicker;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\FactureRecueExport;
use Illuminate\Support\Carbon;

class ListFactureRecues extends ListRecords
{
    protected static string $resource = FactureRecueResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('export_tva')
                ->form([
                    DatePicker::make('date_debut')
                        ->label('Date début')
                        ->required(),
                    DatePicker::make('date_fin')
                        ->label('Date fin')
                        ->required(),
                ])
                ->label('Exporter')
                ->color('warning')
                ->icon('heroicon-o-arrow-down-tray')
                ->action(function (array $data) {
                    $factures = FactureRecue::whereBetween('date', [$data['date_debut'], $data['date_fin']])
                        ->get();
                    
                    return Excel::download(new FactureRecueExport($factures), 'export-factures-pic-' . Carbon::now()->format('Y-m-d') . '.xlsx');
                }),
        ];
    }
}
