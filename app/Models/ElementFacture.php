<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ElementFacture extends Model
{
    use HasFactory;

    protected $table = 'element_factures';
    protected $fillable = ['description', 'prix_unitaire_ttc', 'tva', 'quantite', 'facture_id'];
}
