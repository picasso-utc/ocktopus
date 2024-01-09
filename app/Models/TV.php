<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\belongsTo;

class Tv extends Model
{
    use HasFactory;
    protected $table = 'tv';
    protected $fillable = ['name', 'link_id'];
    public function link(): belongsTo
    {
        return $this->belongsTo(Link::class);
    }
}
