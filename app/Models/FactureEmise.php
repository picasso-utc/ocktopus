<?php

namespace App\Models;

use App\Models\Treso\Semestre;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FactureEmise extends Model
{
    use HasFactory;

    protected $fillable = [
        'tva', 'prix', 'destinataire', 'date_creation', 'nom_createur',
        'date_paiement', 'date_due', 'etat', 'semestre_id'
    ];

    // Valeurs possibles pour la colonne 'etat'
    const FACTURE_DUE = 'D';
    const FACTURE_ANNULEE = 'A';
    const FACTURE_PARTIELLEMENT_PAYEE = 'T';
    const FACTURE_PAYEE = 'P';

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
