<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ElosResource\Pages;
use App\Filament\Admin\Resources\ElosResource\RelationManagers;
use App\Models\ClassementElo;
use App\Models\Elos;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ElosResource extends Resource
{
    protected static ?string $model = ClassementElo::class;

    protected static ?string $navigationIcon = 'heroicon-o-trophy';

    protected static ?string $navigationGroup = 'Gestion de l\'application mobile';

    protected static ?string $navigationLabel = 'Classement Elo';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nom_user')
                    ->label('Personne')
                    ->searchable(),
                Tables\Columns\TextColumn::make('points')
                    ->label('Points')
                    ->sortable()
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListElos::route('/'),
        ];
    }
}
