<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MontantCategorie extends Model
{
    use HasFactory;

    protected $table = 'montant_categorie';
    protected $fillable = ['categorie_id', 'prix', 'facture_id'];

    // Ajoutez une relation avec CategorieFactureRecue
    public function categorie()
    {
        return $this->belongsTo(CategorieFacture::class, 'categorie_id');
    }

    // Définissez la contrainte unique sur les colonnes 'categorie_id' et 'facture_id'
    public function uniqueCategorieForFacture()
    {
        return $this->unique(['categorie_id', 'facture_id'], 'unique_categorie_for_facture');
    }
}
