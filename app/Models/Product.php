<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['name', 'price', 'category', 'active'];

    protected $casts = [
        'active' => 'boolean',
        'price' => 'integer',
    ];
}
