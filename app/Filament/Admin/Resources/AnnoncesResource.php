<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\AnnoncesResource\Pages;
use App\Enums\MediaType;
use App\Models\Annonces;
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

class AnnoncesResource extends Resource
{
    protected static ?string $model = Annonces::class;

    protected static ?string $navigationIcon = 'heroicon-o-megaphone';

    protected static ?string $navigationGroup = 'Gestion de l\'application mobile';

    protected static ?string $navigationLabel = 'Annonces';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('titre')
                    ->required()
                    ->label('Titre de l\'annonce')
                    ->placeholder('Court mais catchy (50 caractères max)'),
                TextInput::make('type')
                    ->required()
                    ->label('Type de l\'annonce')
                    ->placeholder('Type de l\'annonce (Évènement, Nouveauté, Annonce...)'),
                TextInput::make('courte_desc')
                    ->required()
                    ->label('Description courte')
                    ->placeholder('Un description courte (250 caractères max)'),
                TextInput::make('longue_desc')
                    ->required()
                    ->label('Description longue')
                    ->placeholder('Un description longue pour expliquer l\'annonce'),
                Toggle::make('mis_en_avant')->required(),
                FileUpload::make('media_path')
                    ->label('Média (10Mo max)')
                    ->required()
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp']),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('titre')->sortable()->searchable(),
                TextColumn::make('type')->dateTime(),
                TextColumn::make('courte_desc')->dateTime(),
                TextColumn::make('longue_desc')->dateTime(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAnnonces::route('/'),
            'test' => Pages\CreateAnnonces::route('/create'),
            'edit' => Pages\EditAnnonces::route('/{record}/edit'),
        ];
    }
}
