<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Creneau extends Model
{
    protected $table = 'creneau';
    protected $fillable = ['date', 'creneau'];

    public function perm()
    {
        return $this->belongsTo(Perm::class);
    }

    public function astreintes()
    {
        return $this->hasMany(Astreinte::class);
    }


    //    protected static function booted()
    //    {
    //        static::saving(function ($creneau) {
    //           $creneau->week_number = $creneau->date;
    //           $creneau->day_of_week = $creneau->date; // 'l' donne le nom du jour de la semaine
    //
    //                // Vérifier si la perm_id existe déjà dans la base de données
    //            $existingCount = self::where('perm_id', $creneau->perm_id)->count();
    //
    //            // Si la perm_id existe déjà pour trois creneaus ou plus, annuler la sauvegarde
    //            if ($existingCount >= 3) {
    //                    throw new \Exception('Une perm ne peut pas être associée à plus de trois creneaus.');
    //            }
    //        });
    //    }



}
