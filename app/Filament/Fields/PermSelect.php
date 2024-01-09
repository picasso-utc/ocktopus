<?php

namespace App\Filament\Fields;

use App\Models\Perm;
use Filament\Forms\Components\Select;

class PermSelect extends Select
{
    protected function setUp(): void
    {
        $this->options(Perm::all()->pluck('nom', 'id'));
    }
}
