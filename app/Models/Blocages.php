<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blocages extends Model
{
    use HasFactory;

    protected $fillable = [
        'cas',
        'reason',
        'date',
        'fin'
    ];

    protected $casts = [
        'date' => 'datetime',
        'fin' => 'datetime',
    ];
}
