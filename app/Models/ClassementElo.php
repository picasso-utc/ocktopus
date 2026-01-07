<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassementElo extends Model
{
    use HasFactory;

    protected $fillable = [
        'mail_user',
        'nom_user',
        'elo_score',
        'type'
    ];
}
