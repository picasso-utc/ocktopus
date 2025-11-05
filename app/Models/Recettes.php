<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recettes extends Model
{
    use HasFactory;

    protected $fillable = [
        'categorie_id','date_debut','date_fin','valeur','tva','remarque','semestre_id'
    ];

    public function categorie()
    {
        return $this->belongsTo(CategorieFacture::class, 'categorie_id');
    }

    public function semestre()
    {
        return $this->belongsTo(Semestre::class, 'semestre_id');
    }

    public function getPriceWithoutTaxes()
    {
        return round($this->valeur * (100 / (100 + $this->tva)), 2);
    }
    
    public function getTotalTaxes()
    {
        return round($this->valeur - $this->getPriceWithoutTaxes(), 2);
    }
}
