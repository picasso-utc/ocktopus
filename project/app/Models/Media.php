<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Media extends Model
{
    use HasFactory;
    protected $table = 'media';

    protected $fillable = [
        'name',
        'media_type',
        'media_path',
        'activate',
        'times',
        'duree',
    ];

}
