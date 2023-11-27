<?php

namespace App\Models\Treso;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategorieFactureRecue extends Model
{
    use HasFactory;

    protected $table = 'categorie_facture_recues';
    protected $fillable = ['nom', 'code'];
}
