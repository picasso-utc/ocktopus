<?php

namespace App\Models;

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


}
