<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Astreinte;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class   NoteAstreinteOverview extends BaseWidget
{
    protected $__name = "test";
    protected function getStats(): array
    {
        $userId = 1; //A CHANGER

        // Nombre d'astreintes par type pour l'utilisateur spécifié
        $astreintesByType = Astreinte::query()
            ->where('member_id', $userId)
            ->selectRaw('astreinte_type, COUNT(*) as count')
            ->groupBy('astreinte_type')
            ->get()
            ->pluck('count', 'astreinte_type');

        // Nombre moyen d'astreintes par utilisateur
        $totalAstreintes = Astreinte::query()->count();
        $totalUsers = User::query()->count();
        $averageAstreintesPerUser = $totalUsers > 0 ? $totalAstreintes / $totalUsers : 0;

        // Somme des astreintes pour les types "Matin 1" et "Matin 2"
        $sumMatin1Matin2 = $astreintesByType->get('Matin 1', 0) + $astreintesByType->get('Matin 2', 0);
        // Somme des astreintes pour les types "Midi 1" et "Midi 2"
        $sumMidi1Midi2 = $astreintesByType->get('Déjeuner 1', 0) + $astreintesByType->get('Déjeuner 2', 0);
        // Somme des astreintes pour les types "Soir 1" et "Soir 2"
        $sumSoir1Soir2 = $astreintesByType->get('Soir 1', 0) + $astreintesByType->get('Soir 2', 0);


        $astreintes = Astreinte::all();
        // Initialiser le total des points
        $totalPoints = 0;
        // Calculer le nombre total de points pour toutes les astreintes
        foreach ($astreintes as $astreinte) {
            $totalPoints += $astreinte->points;
        }
        $moyenneGenerale = $totalPoints / User::query()->count();


        $totalPointsUtilisateur = User::find($userId)->nombre_points;
        $couleurPoints = $totalPointsUtilisateur < $moyenneGenerale ? 'danger' : 'success';

        $nombreAstreintesNotees = Astreinte::whereNotNull('note_organisation')->count();
        $pourcentageAstreintesNotees = $totalAstreintes > 0
            ? ($nombreAstreintesNotees / $totalAstreintes) * 100
            : 0;
        $couleurPourcentage = 'success' ;
        if ($pourcentageAstreintesNotees < 75) $couleurPourcentage =  'danger';
        else if ($couleurPourcentage > 90)$couleurPourcentage = 'success';
        return [
            Stat::make('Astreintes', Astreinte::query()->where('member_id', 1)->count()) //Filament Auth
                ->description('Votre nombre d\'astreintes'),
            Stat::make('Astreintes matin', $sumMatin1Matin2)
                ->description('Votre nombre d\'astreintes du matin'),
            Stat::make('Astreintes déjeuner', $sumMidi1Midi2)
                ->description('Votre nombre d\'astreintes du déjeuner'),
            Stat::make('Astreintes soir', $sumSoir1Soir2)
                ->description('Votre nombre d\'astreintes du soir'),
            Stat::make('Astreintes', round($averageAstreintesPerUser, 2))
                ->description('Nombre moyen d\'astreintes par utilisateur'),
            Stat::make('Nombre total de points',$totalPointsUtilisateur )
                ->color($couleurPoints)
                ->description('Total des points basé sur les astreintes'),
            Stat::make('Moyenne générale des points par utilisateur', round($moyenneGenerale, 2))
                ->description('Moyenne basée sur les astreintes de tous les utilisateurs'),
            Stat::make('Astreintes notées', round($pourcentageAstreintesNotees, 2) . '%')
                ->description('Pourcentage d\'astreintes notée')
                ->color($couleurPourcentage),

        ];
    }
}
