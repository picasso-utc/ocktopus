<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\AnnoncesResource\Pages;
use App\Enums\MediaType;
use App\Models\Annonces;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
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
                    ->placeholder('Court mais catchy')
                    ->maxLength(50)
                    ->columnSpan(1),
                TextInput::make('type')
                    ->required()
                    ->label('Type de l\'annonce')
                    ->placeholder('Type de l\'annonce (Évènement, Nouveauté, Annonce...)')
                    ->maxLength(50)
                    ->columnSpan(5),
                TextInput::make('courte_desc')
                    ->required()
                    ->label('Description courte')
                    ->placeholder('Un description courte')
                    ->maxLength(250)
                    ->columnSpan(5),
                Toggle::make('mis_en_avant')
                    ->required()
                    ->columnSpan(1),
                TextInput::make('longue_desc')
                    ->required()
                    ->label('Description longue')
                    ->placeholder('Un description longue pour expliquer l\'annonce')
                    ->columnSpan([
                        'sm' => 6,
                        'md' => 6,
                        'lg' => 6,
                        'xl' => 6,
                        '2xl' => 6,
                    ]),
                FileUpload::make('media_path')
                    ->label('Média (10Mo max)')
                    ->required()
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp'])
                    ->columnSpan(4),
                Toggle::make('is_active')
                    ->default(true)
                    ->label("Rendre l'annonce active ?")
                    ->columnSpan(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ToggleColumn::make('is_active')
                    ->label('Annonce active ?'),
                TextColumn::make('titre')->sortable()->searchable(),
                TextColumn::make('type'),
                TextColumn::make('courte_desc'),
                TextColumn::make('longue_desc'),
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
