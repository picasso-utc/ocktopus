<?php

namespace App\Filament\Admin\Pages;

use App\Models\Exoneration;
use App\Services\PayUtcClient;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;

/**
 * Class ExonerationsAnalyse
 *
 * Cette classe représente une page personnalisée dans l'interface Filament pour analyser
 * les exonérations entre deux dates données. Elle permet de filtrer les exonérations
 * et d'afficher des statistiques sur les articles concernés.
 */
class ExonerationsAnalyse extends Page
{
    /**
     * Icône de navigation de la page.
     *
     * @var string|null
     */
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    /**
     * Titre de la page.
     *
     * @var string|null
     */
    protected static ?string $title = 'Filtrer les Exonérations';

    /**
     * Vue associée à cette page.
     *
     * @var string
     */
    protected static string $view = 'filament.admin.pages.exonerations-analyse';

    /**
     * Date de début pour le filtre des exonérations.
     *
     * @var string|null
     */
    public $date_debut;

    /**
     * Date de fin pour le filtre des exonérations.
     *
     * @var string|null
     */
    public $date_fin;

    /**
     * Résultat du filtrage des exonérations, contenant les articles et leurs statistiques.
     *
     * @var array
     */
    public $exonerationsCount = [];

    /**
     * Retourne le schéma du formulaire pour sélectionner les dates de début et de fin.
     *
     * @return array
     */
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

    /**
     * Calcule les exonérations entre deux dates sélectionnées.
     *
     * Valide les entrées utilisateur, récupère les statistiques des exonérations groupées
     * par article ID, et enrichit les données avec les noms des articles via une API externe.
     *
     * @return void
     */
    public function calculerExonerations(): void
    {
        // Validation des dates de début et de fin
        $this->validate([
            'date_debut' => 'required|date',
            'date_fin' => 'required|date',
        ]);

        // Requête pour récupérer les exonérations groupées par article_id
        $exonerations = DB::table('exonerations')
            ->select('article_id', DB::raw('COUNT(*) as total'))
            ->whereBetween('date', [$this->date_debut, $this->date_fin])
            ->groupBy('article_id')
            ->get();

        // Création d'un client pour interroger l'API PayUtc
        $client = new PayUtcClient();

        // Enrichissement des données avec le nom des articles
        $this->exonerationsCount = $exonerations->map(function ($exon) use ($client) {
            // Récupération des données de l'article via l'API
            $response = $client->makePayutcRequest('GET', "products/{$exon->article_id}", []);
            $responseData = json_decode($response->getContent(), true);

            // Retour des données enrichies
            return [
                'article_id' => $exon->article_id,
                'name' => $responseData['name'] ?? 'Nom inconnu', // Nom de l'article
                'total' => $exon->total, // Nombre d'exonérations pour cet article
            ];
        });
    }
}
