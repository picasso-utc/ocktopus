<?php

namespace App\Filament\Fields;

use App\Enums\MemberRole;
use Filament\Forms\Components\Select;

class UserRoleSelect extends Select
{
    public function setUp(): void
    {
        $this->options(enum_pluck(MemberRole::class));
        $this->label('Rôle');
        $this->required();
        $this->default('none');
    }
}
