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
            Actions\Action::make('copier_extes_du_jour')
                ->label('Copier extés du jour')
                ->color('info')
                ->icon('heroicon-o-clipboard-document')
                ->action(function () {
                    $today = Carbon::today();

                    $extes = Exte::where('exte_date_debut', '<=', $today)
                        ->where('exte_date_fin', '>=', $today)
                        ->get();

                    $dateLabel = $today->translatedFormat('l d F Y');

                    $lines = ["------ {$dateLabel} ------"];
                    foreach ($extes as $exte) {
                        $lines[] = "{$exte->exte_nom_prenom} - exté de {$exte->etu_nom_prenom}";
                    }

                    $text = implode("\n", $lines);

                    $this->js("navigator.clipboard.writeText(" . json_encode($text) . ")");
                })
                ->successNotificationTitle('Extés du jour copiées dans le presse-papier !'),

            Actions\Action::make('pdf_validés')
                ->form([
                    DatePicker::make('date_debut')
                        ->label('Date début')
                        ->required(),
                    DatePicker::make('date_fin')
                        ->label('Date fin')
                        ->required(),
                ])
                ->label('Générer PDF Validés')
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
                            ->setPaper('a4', 'landscape')
                            ->stream();
                    }, 'Demandes-' . now()->format('Y-m-d') . '.pdf');
                }),
            Actions\Action::make('pdf_a_validé')
                ->form([
                    DatePicker::make('date_debut')
                        ->label('Date début')
                        ->required(),
                    DatePicker::make('date_fin')
                        ->label('Date fin')
                        ->required(),
                ])
                ->label('Générer PDF A Validé')
                ->color('warning')
                ->icon('heroicon-o-arrow-down-tray')
                ->action(function (array $data) {
                    $requests = Exte::where('mailed', 0)
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
                            ->setPaper('a4', 'landscape')
                            ->stream();
                    }, 'Demandes-' . now()->format('Y-m-d') . '.pdf');
                })
        ];
    }
}
