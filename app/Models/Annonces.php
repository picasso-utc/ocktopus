<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Annonces extends Model
{
    use HasFactory;
    
    protected $table = 'annonces';

    protected $fillable = [
        'titre',
        'type',
        'courte_desc',
        'longue_desc',
        'mis_en_avant',
        'media_path'
    ];
}
