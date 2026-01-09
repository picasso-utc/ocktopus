<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SignatureCharte extends Model
{
    use HasFactory;

    protected $table = 'signature_chartes';
    protected $fillable = ['adresse_mail', 'semestre_id'];

    public function semestre()
    {
        return $this->belongsTo(Semestre::class, 'semestre_id');
    }

}
