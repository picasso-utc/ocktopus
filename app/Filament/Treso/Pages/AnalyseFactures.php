<?php

namespace App\Filament\Treso\Pages;

use App\Models\CategorieFacture;
use App\Models\MontantCategorie;
use App\Models\FactureRecue;
use App\Models\Recettes;
use Filament\Pages\Page;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Grid;

class AnalyseFactures extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $title = 'Analyse Générale';
    protected static string $view = 'filament.treso.pages.analyse-factures';

    public $date_debut;
    public $date_fin;
    public $date_facture = 'date';
    public $date_recette = 'date_fin';
    public $totalsByCategory = [];
    public $totalRecettes = 0;
    public $totalDepenses = 0;

    protected function getFormSchema(): array
    {
        return [
            Grid::make()
                ->schema([
                    DatePicker::make('date_debut')
                        ->label('Date de début')
                        ->required()
                        ->columnSpan(1),
    
                    DatePicker::make('date_fin')
                        ->label('Date de fin')
                        ->required()
                        ->columnSpan(1),
    
                    Select::make('date_facture')
                        ->label('Date pour la facture')
                        ->options([
                            'date' => 'Date de facturation',
                            'date_paiement' => 'Date de paiement',
                            'date_remboursement' => 'Date de remboursement',
                        ])
                        ->required()
                        ->columnSpan(1), 
    
                    Select::make('date_recette')
                        ->label('Date pour la recette')
                        ->options([
                            'date_debut' => 'Date de début',
                            'date_fin' => 'Date de fin',
                        ])
                        ->required()
                        ->columnSpan(1),
                ]),
        ];
    }    

    public function calculerTotal()
    {
        $this->totalDepenses = FactureRecue::whereBetween($this->date_facture, [$this->date_debut, $this->date_fin])
            ->sum('prix');

        $this->totalRecettes = Recettes::whereBetween($this->date_recette, [$this->date_debut, $this->date_fin]) 
            ->sum('valeur');
    }

    public function calculerTotaux()
    {
        $recettesByCategory = CategorieFacture::with(['recettes' => function ($query) {  // On crée un array avec la somme des recettes et TVA par catégorie
                $query->whereBetween($this->date_recette, [$this->date_debut, $this->date_fin]);
            }])
            ->get()
            ->mapWithKeys(function ($categorie) {
                $totalRecettes = $categorie->recettes->sum('valeur');
                $totalTvaRecettes = $categorie->recettes->sum('tva');
                return [
                    $categorie->id => [
                        'categorie' => $categorie->nom,
                        'totalRecettes' => $totalRecettes,
                        'totalTvaRecettes' => $totalTvaRecettes,
                        'totalDepenses' => 0,  // On init les champ pour les dépenses à 0 dans le array pour merge après
                        'totalTvaDepenses' => 0
                    ],
                ];
            })
            ->toArray();
    
        $depensesByCategory = MontantCategorie::with('categorie')  // idem ici mais avec les dépenses
            ->whereHas('facture', function ($query) {
                $query->whereBetween($this->date_facture, [$this->date_debut, $this->date_fin]);
            })
            ->selectRaw('categorie_id, SUM(prix) as total_prix, SUM(tva) as total_tva')
            ->groupBy('categorie_id')
            ->get();
    
        foreach ($depensesByCategory as $depense) {  // on merge les array
            if (isset($recettesByCategory[$depense->categorie_id])) {
                $recettesByCategory[$depense->categorie_id]['totalDepenses'] = $depense->total_prix;
                $recettesByCategory[$depense->categorie_id]['totalTvaDepenses'] = $depense->total_tva;
            } else {
                $recettesByCategory[$depense->categorie_id] = [
                    'categorie' => $depense->categorie->nom,
                    'totalRecettes' => 0,
                    'totalTvaRecettes' => 0,
                    'totalDepenses' => $depense->total_prix,
                    'totalTvaDepenses' => $depense->total_tva,
                ];
            }
        }
    
        $this->totalsByCategory = array_values($recettesByCategory);
    }    
}
