<?php

namespace App\Models\Treso;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FactureRecue extends Model
{
    use HasFactory;
    protected $fillable = [
        'tva', 'prix', 'perm_id', 'etat', 'nom_entreprise', 'date',
        'date_created', 'date_paiement', 'date_remboursement', 'moyen_paiement',
        'personne_a_rembourser', 'immobilisation', 'remarque', 'semestre_id', 'facture_number'
    ];

    // Valeurs possibles pour la colonne 'etat'
    const ETAT_FACTURE_A_PAYER = 'D';
    const ETAT_FACTURE_A_REMBOURSER = 'R';
    const ETAT_FACTURE_EN_ATTENTE = 'E';
    const ETAT_FACTURE_PAYEE = 'P';

    public function perm()
    {
        return $this->belongsTo(Creneau::class, 'perm_id');
    }

    public function semestre()
    {
        return $this->belongsTo(Semestre::class, 'semestre_id');
    }

    public function getPriceWithoutTaxes()
    {
        return round($this->prix * (100 / (100 + $this->tva)), 2);
    }

    public function getTotalTaxes()
    {
        return round($this->prix - $this->getPriceWithoutTaxes(), 2);
    }

}
