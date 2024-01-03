<?php

namespace App\Filament\Admin\Resources\AstreinteResource\Pages;

use App\Filament\Admin\Resources\AstreinteResource;
use Filament\Actions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;

class CreateAstreinte extends CreateRecord
{
    protected static string $resource = AstreinteResource::class;

}
