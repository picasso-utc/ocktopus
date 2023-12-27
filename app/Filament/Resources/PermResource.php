<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PermResource\Pages;
use App\Filament\Resources\PermResource\RelationManagers;
use App\Models\Perm;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PermResource extends Resource
{
    protected static ?string $model = Perm::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'Gestion des perms';

    protected static ?string $navigationLabel = 'Validation des perms';



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
                Tables\Columns\TextColumn::make('nom')
                    ->label('Nom')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('theme')
                    ->label('Thème'),
                Tables\Columns\BooleanColumn::make('asso')
                    ->label('Asso')
                    ->sortable(),
                Tables\Columns\TextColumn::make('ambiance')
                    ->label('Ambiance')
                    ->sortable(),
                Tables\Columns\TextColumn::make('periode')
                    ->label('Periode')
                    ->sortable(),
                Tables\Columns\CheckboxColumn::make('validated')
                    ->label('Validation')


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
            'index' => Pages\ListPerms::route('/'),
            'create' => Pages\CreatePerm::route('/create'),
            'edit' => Pages\EditPerm::route('/{record}/edit'),
        ];
    }
}
