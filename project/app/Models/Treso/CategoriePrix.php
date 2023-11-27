<?php

namespace App\Models\Treso;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoriePrix extends Model
{
    use HasFactory;

    protected $table = 'categorie_prix';
    protected $fillable = ['categorie_id', 'prix', 'facture_id'];

    // Ajoutez une relation avec CategorieFactureRecue
    public function categorie()
    {
        return $this->belongsTo(CategorieFactureRecue::class, 'categorie_id');
    }

    // Ajoutez une relation avec FactureRecue
    public function facture()
    {
        return $this->belongsTo(FactureRecue::class, 'facture_id');
    }

    // Définissez la contrainte unique sur les colonnes 'categorie_id' et 'facture_id'
    public function uniqueCategorieForFacture()
    {
        return $this->unique(['categorie_id', 'facture_id'], 'unique_categorie_for_facture');
    }
}
