<?php

namespace App\Models\Treso;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuivieModificationFacture extends Model
{
    use HasFactory;

    protected $fillable = ['facture_number', 'action', 'login', 'date_creation'];

    // Définissez les valeurs possibles pour la colonne 'action'
    const CREATION = 'C';
    const SUPPRESSION = 'S';
    const MISE_A_JOUR = 'M';

    public function getActionLabelAttribute()
    {
        // Ajoutez cette méthode pour obtenir une représentation lisible de l'action
        $actionLabels = [
            self::CREATION => 'Création de la facture',
            self::SUPPRESSION => 'Suppression de la facture',
            self::MISE_A_JOUR => 'Mise à jour de la facture',
        ];

        return $actionLabels[$this->action] ?? 'Inconnu';
    }
}
