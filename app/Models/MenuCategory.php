<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuCategory extends Model
{
    protected $fillable = ['name', 'icon', 'product_categories', 'sort_order'];

    protected $casts = [
        'product_categories' => 'array',
    ];
}
