<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    use HasFactory;
    protected $table = 'media'; // Nom de la table dans la base de données

    protected $fillable = [
        'name',
        'media_type',
        'media',
        'activate',
        'times',
    ];
}
