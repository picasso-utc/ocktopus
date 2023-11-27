<?php

namespace App\Models\Treso;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SousCategoriePrix extends Model
{
    use HasFactory;

    protected $table = 'sous_categorie_prix';
    protected $fillable = ['sous_categorie_id', 'prix', 'facture_id'];

    // Ajoutez une relation avec SousCategorieFactureRecue
    public function sousCategorie()
    {
        return $this->belongsTo(SousCategorieFactureRecue::class, 'sous_categorie_id');
    }

    // Ajoutez une relation avec FactureRecue
    public function facture()
    {
        return $this->belongsTo(FactureRecue::class, 'facture_id');
    }
}
