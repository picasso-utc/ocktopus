<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SemesterEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'titre',
        'date',
        'lieu',
        'description'
    ];
}
