<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoodieWinner extends Model
{
    use HasFactory;

    protected $fillable = ['PickedUp', 'Winner'];

    public function setPickedUpAttribute($value)
    {
        $this->attributes['PickedUp'] = (bool) $value;
    }

    public function setWinnerAttribute($value)
    {
        $this->attributes['Winner'] = $value;
    }
}
