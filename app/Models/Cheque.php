<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cheque extends Model
{
    use HasFactory;

    protected $fillable = [
        'num', 'valeur', 'state', 'destinataire', 'date_encaissement',
        'date_emission', 'commentaire', 'facture_id'
    ];

    // Définissez les valeurs possibles pour la colonne 'state'
    const CHEQUE_ENCAISSE = 'E';
    const CHEQUE_PENDING = 'P';
    const CHEQUE_ANNULE = 'A';
    const CHEQUE_CAUTION = 'C';

    public function facture()
    {
        return $this->belongsTo(FactureRecue::class, 'facture_id');
    }
}
