<?php

namespace App\Filament\Treso\Pages;

use App\Models\MontantCategorie;
use App\Models\FactureRecue;
use Carbon\Carbon;
use Filament\Pages\Page;
use Filament\Forms\Components\DatePicker;

class AnalyseFactures extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $title = 'Analyse des Factures Reçues';
    protected static string $view = 'filament.treso.pages.analyse-factures';

    public $date_debut;
    public $date_fin;
    public $total = 0;
    public $totalsByCategory = [];

    protected function getFormSchema(): array
    {
        return [
            DatePicker::make('date_debut')
                ->label('Date de début')
                ->required(),
            DatePicker::make('date_fin')
                ->label('Date de fin')
                ->required(),
        ];
    }

    public function calculerTotal()
    {
        $this->total = FactureRecue::whereBetween('date', [$this->date_debut, $this->date_fin])
            ->sum('prix');
    }

    public function calculerTotals()
    {
        $this->totalsByCategory = MontantCategorie::with('categorie')
            ->whereHas('facture', function ($query) {
                $query->whereBetween('date', [$this->date_debut, $this->date_fin]);
            })
            ->selectRaw('categorie_id, SUM(prix) as total_prix')
            ->groupBy('categorie_id')
            ->get();
    }
}
