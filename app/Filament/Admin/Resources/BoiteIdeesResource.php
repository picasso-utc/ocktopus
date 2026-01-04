<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\BoiteIdeesResource\Pages;
use App\Enums\MediaType;
use App\Models\BoiteIdees;
use App\Models\Faq;
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
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;

class BoiteIdeesResource extends Resource
{
    protected static ?string $model = BoiteIdees::class;

    protected static ?string $navigationIcon = 'heroicon-o-light-bulb';

    protected static ?string $navigationGroup = 'Gestion de l\'application mobile';

    protected static ?string $navigationLabel = 'Boite à idées';

    public static function form(Form $form): Form
    {
        print(mailToName(auth()->user()?->email));
        return $form
            ->schema([
                TextInput::make('titre')
                    ->placeholder('')
                    ->required(),
                TextInput::make('description')
                    ->required()
                    ->placeholder('Décrit ici ton idées'),
                TextInput::make('author')
                    ->default(fn () => mailToName(auth()->user()?->email))
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ToggleColumn::make('readed')
                    ->label('Idée Lue ?'),
                TextColumn::make('titre')->sortable()->searchable(),
                TextColumn::make('author')->sortable()->searchable(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBoiteIdees::route('/'),
            'test' => Pages\CreateBoiteIdees::route('/create'),
            'edit' => Pages\EditBoiteIdees::route('/{record}/edit'),
        ];
    }
}

