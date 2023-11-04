<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Tv extends Model
{
    use HasFactory;
    public function link(): HasOne{
        return $this->HasOne(Link::class);
    }
}
