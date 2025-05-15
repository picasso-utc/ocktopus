<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shotgun extends Model
{
    use HasFactory;

    protected $table = 'shotgun';
    protected $fillable = ['email','events_id'];

    public function event()
    {
        return $this->belongsTo(Events::class, 'events_id');
    }
}
