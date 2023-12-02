<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\MediaType;

class Media extends Model
{
    use HasFactory;
    protected $table = 'media';

    protected $fillable = [
        'name',
        'media_type',
        'media_path',
        'activated',
        'times',
        'duree',
    ];
    protected $enums = [
        'media_type' => MediaType::class,
    ];

}
