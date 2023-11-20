<?php

namespace App\Enums;

enum MemberRole : string
{
    case Administrator = 'admin';
    case Member = 'member';
    case None = 'none';

    public function isAdministrator(): bool
    {
        return $this === self::Administrator;
    }

    public function isMember(): bool
    {
        return $this === self::Member || $this->isAdministrator();
    }

    public function title(): string
    {
        return match ($this) {
            self::Administrator => 'Administrateur',
            self::Member => 'Membre',
            self::None => 'Aucun droit',
        };
    }
}
