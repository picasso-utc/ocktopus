<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\AstreinteType;

class Astreinte extends Model
{
    protected $fillable = [
        'member_id',
        'creneau_id',
        'astreinte_type',
        'note_deco',
        'note_orga',
        'note_anim',
        'note_menu',
        'commentaire',
    ];

    public function member()
    {
        return $this->belongsTo(User::class);
    }

    public function creneau()
    {
        return $this->belongsTo(Creneau::class);
    }


    public function getPointsAttribute()
    {
        switch ($this->astreinte_type) {
        case 'Matin 1':
        case 'Matin 1':
        case 'Déjeuner 1':
        case 'Déjeuner 2':
        case 'Autre':
            return 1; // 1 point pour le matin et midi
        case 'Soir 1':
            return 2.5; // 2.5 points pour le soir_1
        case 'Soir 2':
            return 2; // 2 points pour les autres soirs
        default:
            return 0; // ou une valeur par défaut
        }
    }
}

