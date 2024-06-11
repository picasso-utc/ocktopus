<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ElementNoteDeFrais extends Model
{
    use HasFactory;

    protected $table = 'element_note_de_frais';
    protected $fillable = ['description', 'prix_unitaire_ttc', 'tva', 'quantite', 'note_de_frais_id'];
}
