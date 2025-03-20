<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\EventResource\Pages;
use App\Filament\Admin\Resources\EventResource\RelationManagers;
use App\Models\Events;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EventResource extends Resource
{
    protected static ?string $model = Events::class;

    protected static ?string $navigationIcon = 'heroicon-o-megaphone';
    protected static ?string $navigationGroup = 'Général';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('titre')->required(),
                DateTimePicker::make('ouverture')->required(),
                DateTimePicker::make('debut_event')->nullable(),
                DateTimePicker::make('fin_event')->nullable(),
                TextInput::make('nombre_places')->numeric()->required(),
            ]);
    }
    
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('titre')->sortable()->searchable(),
                TextColumn::make('ouverture')->dateTime(),
                TextColumn::make('debut_event')->dateTime(),
                TextColumn::make('fin_event')->dateTime(),
                TextColumn::make('nombre_places'),
                TextColumn::make('shotguns_count')
                    ->label('Places restantes')
                    ->getStateUsing(fn ($record) => $record->nombre_places - $record->shotguns()->count()),
            ])
            ->filters([
                Filter::make('En cours')
                    ->query(fn ($query) => $query->where('debut_event', '>=', now())),
            ]);
    }    

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEvents::route('/'),
            'create' => Pages\CreateEvent::route('/create'),
            'edit' => Pages\EditEvent::route('/{record}/edit'),
        ];
    }
}
