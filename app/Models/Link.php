<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Link extends Model
{
    use HasFactory;

    protected $table = 'link';
    protected $fillable = [
        'name',
        'url',
    ];
    public function tvs(): HasMany
    {
        return $this->HasMany(Tv::class);
    }
}
