<?php

namespace App\Filament\Fields;

use App\Models\Link;
use Filament\Forms\Components\Select;

class LinkSelect extends Select
{
    protected function setUp(): void
    {
        $this->options(Link::all()->pluck('name', 'id'));
    }
}
