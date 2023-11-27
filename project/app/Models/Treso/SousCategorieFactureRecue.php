<?php

namespace App\Models\Treso;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SousCategorieFactureRecue extends Model
{
    use HasFactory;

    protected $table = 'sous_categorie_facture_recues';
    protected $fillable = ['categorie_id', 'code', 'nom'];

    // Ajoutez une relation avec Categorie
    public function categorie()
    {
        return $this->belongsTo(CategorieFactureRecue::class, 'categorie_id');
    }

    // Fonction pour afficher le code dans les controllers
    public function __toString()
    {
        return $this->categorie->nom . $this->nom;
    }

    // Définissez la contrainte unique sur les colonnes 'categorie_id' et 'code'
    public function uniqueCodeForSousCategorie()
    {
        return $this->unique(['categorie_id', 'code'], 'unique_code_for_sous_categorie');
    }
}
