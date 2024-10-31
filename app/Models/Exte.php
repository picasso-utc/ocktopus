<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exte extends Model
{
    use HasFactory;

    protected $fillable = [
        'etu_nom_prenom',
        'etu_cas',
        'etu_mail',
        'exte_nom_prenom',
        'exte_date_debut',
        'exte_date_fin',
        'responsabilite',
        'commentaire',
    ];

    protected $dates = [
        'exte_date_debut',
        'exte_date_fin',
    ];
}
