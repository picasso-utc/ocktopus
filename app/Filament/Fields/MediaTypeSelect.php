<?php

namespace App\Filament\Fields;

use App\Enums\MediaType;
use Filament\Forms\Components\Select;

class MediaTypeSelect extends Select
{
    public function setUp(): void
    {
        $this->options(enum_pluck(MediaType::class));
        $this->label('Type');
        $this->required();
        $this->reactive();
    }
}
