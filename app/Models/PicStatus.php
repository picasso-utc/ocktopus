<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PicStatus extends Model
{
    protected $table = 'pic_status';

    protected $fillable = ['open'];

    protected $casts = [
        'open' => 'boolean',
    ];

    public static function current(): self
    {
        return static::firstOr(function () {
            $status = new self(['open' => false]);
            $status->id = 1;
            $status->save();

            return $status;
        });
    }
}
