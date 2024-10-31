<?php

namespace App\Filament\Admin\Resources\ExteResource\Pages;

use App\Filament\Admin\Resources\ExteResource;
use App\Models\Exte;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Blade;
use Filament\Forms\Components\DatePicker;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class ListExtes extends ListRecords
{
    protected static string $resource = ExteResource::class;


    public function getTabs(): array
    {
        $startOfWeek = now()->startOfWeek();
        return [
            // Élément pour les éléments en attente
            'waiting' => Tab::make('En attente')
                ->modifyQueryUsing(
                    function (Builder $query) use ($startOfWeek) {
                        return $query->where('mailed', 0)
                        ->where('exte_date_fin', '>=', $startOfWeek);
                    }
                ),
            // Élément pour les éléments validés
            'validated' => Tab::make('Validées')
                ->modifyQueryUsing(
                    function (Builder $query) use ($startOfWeek) {
                        return $query->where('mailed', 1)
                            ->where('exte_date_fin', '>=', $startOfWeek);
                    }
                ),
        ];
    }



protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('pdf')
                ->form([
                    DatePicker::make('date_debut')
                        ->label('Date début')
                        ->required(),
                    DatePicker::make('date_fin')
                        ->label('Date fin')
                        ->required(),
                ])
                ->label('Générer PDF')
                ->color('success')
                ->icon('heroicon-o-arrow-down-tray')
                ->action(function (array $data) {
                    $requests = Exte::where('mailed', 1)
                        ->where(function ($query) use ($data) {
                            $query->whereBetween('exte_date_debut', [$data['date_debut'], $data['date_fin']])
                                ->orWhereBetween('exte_date_fin', [$data['date_debut'], $data['date_fin']])
                                ->orWhere(function ($query) use ($data) {
                                    $query->where('exte_date_debut', '<=', $data['date_debut'])
                                        ->where('exte_date_fin', '>=', $data['date_fin']);
                                });
                        })
                        ->get();
                    return response()->streamDownload(function () use ($requests) {
                        echo Pdf::loadHtml(
                            Blade::render('pdf/exteTab', ['demandes' => $requests])
                        )
                            ->setPaper('a4','landscape')
                            ->stream();
                    }, 'Demandes-' . now()->format('Y-m-d') . '.pdf');
                })
        ];
    }
}
