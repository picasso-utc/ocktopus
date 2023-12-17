<?php

namespace App\Filament\Resources;

use App\Filament\Fields\LinkSelect;
use App\Filament\Resources\TvSetupResource\Pages;
use App\Filament\Resources\TvSetupResource\RelationManagers;
use App\Models\Tv;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TvSetupResource extends Resource
{
    protected static ?string $model = Tv::class;
    protected static ?string $navigationIcon = 'heroicon-o-tv';
    protected static ?string $navigationGroup = 'Gestion des télés';
    protected static ?string $navigationLabel = 'Télés';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(50),
                LinkSelect::make('link_id')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->label('Nom de la TV'),
                Tables\Columns\TextColumn::make('link.name')
                    ->searchable()
                    ->sortable()
                    ->label('Nom du lien'),
                Tables\Columns\TextColumn::make('link.url')
                    ->formatStateUsing(function ($state) {
                        return substr($state, 0, 50);
                    })
                    ->label('URL'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageTvSetups::route('/'),
        ];
    }
}