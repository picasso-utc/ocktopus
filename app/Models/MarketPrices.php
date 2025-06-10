<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarketPrices extends Model
{
    use HasFactory;

    protected $table = 'market_prices';
    protected $primaryKey = 'article_id';
    protected $keyType = 'int';
    public $incrementing = false;
    public $timestamps = true;

    protected $fillable = ['article_id', 'price'];
}
