<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use App\Models\Perm;
class Signature extends Model
{
    use HasFactory;

    protected $fillable = ['perm_id', 'adresse_mail' ];

    public function perm()
    {
        return $this->belongsTo(Perm::class, 'perm_id');
    }
}
