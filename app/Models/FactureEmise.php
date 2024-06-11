<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FactureEmise extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom','prenom','numero_voie','rue','code_postal','ville','email',
        'tva', 'prix', 'destinataire', 'date_creation', 'nom_createur',
        'date_paiement', 'date_due', 'state', 'semestre_id'
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

    public function elementFacture()
    {
        return $this->hasMany(ElementFacture::class, 'facture_id');
    }
}
