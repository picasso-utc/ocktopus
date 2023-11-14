<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Media extends Model
{
    use HasFactory;
    protected $table = 'media';

    protected $fillable = [
        'name',
        'media_type',
        'media_path',
        'activate',
        'times',
    ];

    // Méthode pour stocker un média
    public function storeMedia($file)
    {
        $path = $file->store('media');
        $this->media_path = $path;
        $this->save();
    }

}
