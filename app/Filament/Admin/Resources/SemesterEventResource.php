<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\SemesterEventResource\Pages;
use App\Enums\MediaType;
use App\Models\Annonces;
use App\Models\SemesterEvent;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;

class SemesterEventResource extends Resource
{
    protected static ?string $model = SemesterEvent::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'Gestion de l\'application mobile';

    protected static ?string $navigationLabel = 'Évènements du semestre';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('titre')
                    ->required()
                    ->label('Titre de l\'évènement')
                    ->placeholder('Pic d\'or, RDP, Pic Nique en musique...'),
                DatePicker::make('date')
                    ->required()
                    ->label('Date de l\'évènement'),
                TextInput::make('Lieu')
                    ->required()
                    ->placeholder('Picasso, JMDE, Parking...'),
                TextInput::make('description')
                    ->required()
                    ->placeholder('Un description pour expliquer l\'évènement'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('titre')->sortable()->searchable(),
                TextColumn::make('date')->dateTime(),
                TextColumn::make('lieu'),
                TextColumn::make('description'),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSemesterEvent::route('/'),
            'test' => Pages\CreateSemesterEvent::route('/create'),
            'edit' => Pages\EditSemesterEvent::route('/{record}/edit'),
        ];
    }
}
