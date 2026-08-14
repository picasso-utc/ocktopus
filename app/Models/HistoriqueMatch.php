<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoriqueMatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'mail_envoyeur',
        'nom_envoyeur',
        'mail_receveur',
        'nom_receveur',
        'type',
        'gagner',
        'valider',
        'score_envoyeur',
        'score_receveur',
        'elo_delta_envoyeur',
        'elo_delta_receveur',
    ];

    protected $casts = [
        'valider' => 'boolean',
        'gagner' => 'boolean',
    ];
}
