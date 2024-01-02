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
            case AstreinteType::M1:
            case AstreinteType::M2:
            case AstreinteType::D1:
            case AstreinteType::D2:
            case AstreinteType::A:
                return 1; // 1 point pour le matin et midi
            case AstreinteType::S1:
                return 2.5; // 2.5 points pour le soir_1
            case AstreinteType::S2:
            case AstreinteType::S3:
            case AstreinteType::S4:
                return 2; // 2 points pour les autres soirs
            default:
                return 0; // ou une valeur par défaut
        }
    }
}

