<?php

namespace App\Enums;

enum MediaType: string
{
    case Image = 'Image';
    case Video = 'Video';

    public function title(): string
    {
        return match ($this) {
            self::Image => 'Image',
            self::Video => 'Vidéo',
        };
    }
}
