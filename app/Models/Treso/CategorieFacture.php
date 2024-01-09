<?php

namespace App\Models\Treso;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategorieFacture extends Model
{
    use HasFactory;

    protected $table = 'categorie_factures';
    protected $fillable = ['nom', 'code','parent_id'];

    public function parent()
    {
        return $this->belongsTo(CategorieFacture::class, 'id_parent');
    }

    public function nom()
    {
        return $this->nom;
    }

    public function children()
    {
        return $this->hasMany(CategorieFacture::class, 'id_parent');
    }

    public function descendants()
    {
        return $this->children()->with('descendants');
    }
}
