<?php

namespace App\Enums;

class AstreinteType
{
    const M1 = 'M1';
    const M2 = 'M2';
    const D1 = 'D1';
    const D2 = 'D2';
    const S1 = 'S1';
    const S2 = 'S2';
    const S3 = 'S3';
    const S4 = 'S4';
    const A = 'A';
    const LESSIVE = 'Lessive';

    public static function choices()
    {
        return [
            self::M1 => 'Matin 1',
            self::M2 => 'Matin 2',
            self::D1 => 'Déjeuner 1',
            self::D2 => 'Déjeuner 2',
            self::S1 => 'Soir 1',
            self::S2 => 'Soir 2',
            self::S3 => 'Soir 3',
            self::S4 => 'Soir 4',
            self::A => 'Divers',
            self::LESSIVE => 'LESSIVE',
        ];
    }
}
