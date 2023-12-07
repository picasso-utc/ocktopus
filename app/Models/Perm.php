<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Perm extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom', 'theme', 'description', 'periode', 'ambiance',
        'asso', 'nom_resp', 'mail_resp', 'nom_resp_2', 'mail_resp_2', 'mail_asso', 'validated'
    ];


}
