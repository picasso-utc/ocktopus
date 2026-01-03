<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\JeuxTemporaireResource\Pages;
use App\Models\JeuxTemporaire;
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

class JeuxTemporaireResource extends Resource
{
    protected static ?string $model = JeuxTemporaire::class;

    protected static ?string $navigationIcon = 'heroicon-o-puzzle-piece';

    protected static ?string $navigationGroup = 'Gestion de l\'application mobile';

    protected static ?string $navigationLabel = 'Jeux temporaires';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('titre')
                    ->required()
                    ->label('Titre du jeux')
                    ->placeholder("N'oubliez pas les pic'roles, Questions pour un champion..."),
                TextInput::make('date')
                    ->required()
                    ->label('Durée du jeux')
                    ->placeholder('(ex: Du 28 au 31 Octobre - Ticket d\'or à gagner !)'),
                TextInput::make('lieu')
                    ->required()
                    ->label('Lieu')
                    ->placeholder('Picasso, JMDE, Parking...'),
                TextInput::make('description')
                    ->required()
                    ->placeholder('Un description pour expliquer le jeux, comment participer (suivre les story insta etc...)'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('titre')->sortable()->searchable(),
                TextColumn::make('date'),
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
            'index' => Pages\ListJeuxTemporaire::route('/'),
            'test' => Pages\CreateJeuxTemporaire::route('/create'),
            'edit' => Pages\EditJeuxTemporaire::route('/{record}/edit'),
        ];
    }
}
