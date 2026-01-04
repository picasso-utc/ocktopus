<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BoiteIdees extends Model
{
    use HasFactory;
    protected $fillable = ['author', 'titre','description', 'readed'];
}
