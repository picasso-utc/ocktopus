<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Events extends Model
{
    use HasFactory;

    protected $table = 'events';
    protected $fillable = ['titre','ouverture','debut_event','fin_event','nombre_places', 'categorie'];

    public function shotguns()
    {
        return $this->hasMany(Shotgun::class, 'events_id');
    }
}
