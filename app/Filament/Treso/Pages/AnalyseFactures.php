<?php

namespace App\Filament\Treso\Pages;

use App\Models\CategorieFacture;
use App\Models\MontantCategorie;
use App\Models\FactureRecue;
use App\Models\Recettes;
use Filament\Pages\Page;
use Filament\Forms\Components\DatePicker;

class AnalyseFactures extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $title = 'Analyse Générale';
    protected static string $view = 'filament.treso.pages.analyse-factures';

    public $date_debut;
    public $date_fin;
    public $totalsByCategory = [];
    public $totalRecettes = 0;
    public $totalDepenses = 0;

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
        $this->totalDepenses = FactureRecue::whereBetween('date', [$this->date_debut, $this->date_fin])
            ->sum('prix');

        $this->totalRecettes = Recettes::whereBetween('date_fin', [$this->date_debut, $this->date_fin])  // On se base sur la date de fin de la recette pour savoir si on la prend en compte ou pas
            ->sum('valeur');
    }

    public function calculerTotaux()
    {
        $recettesByCategory = CategorieFacture::with(['recettes' => function ($query) {  // Calcule les recettes par catégorie
                $query->whereBetween('date_debut', [$this->date_debut, $this->date_fin]);
            }])
            ->get()
            ->mapWithKeys(function ($categorie) {
                $totalRecettes = $categorie->recettes->sum('valeur');
                return [
                    $categorie->id => [
                        'categorie' => $categorie->nom,
                        'totalRecettes' => $totalRecettes,
                        'totalDepenses' => 0, // On init pour l'instant à 0, le calcul est fait apr.
                    ],
                ];
            })
            ->toArray(); // Convertir en tableau

        $depensesByCategory = MontantCategorie::with('categorie')   // calcule maintenant les dépenses (factures reçues) par catégories
            ->whereHas('facture', function ($query) {
                $query->whereBetween('date', [$this->date_debut, $this->date_fin]);
            })
            ->selectRaw('categorie_id, SUM(prix) as total_prix')
            ->groupBy('categorie_id')
            ->get();

        foreach ($depensesByCategory as $depense) {    // fusionner les res dans un array
            if (isset($recettesByCategory[$depense->categorie_id])) {
                $recettesByCategory[$depense->categorie_id]['totalDepenses'] = $depense->total_prix;
            } else {
                $recettesByCategory[$depense->categorie_id] = [
                    'categorie' => $depense->categorie->nom,
                    'totalRecettes' => 0,
                    'totalDepenses' => $depense->total_prix,
                ];
            }
        }

        $this->totalsByCategory = array_values($recettesByCategory);
    }
}
