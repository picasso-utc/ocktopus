<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\PermResource\Pages;
use App\Filament\Admin\Resources\PermResource\RelationManagers;
use App\Models\Perm;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
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

    protected static ?string $navigationLabel = 'Validations des perms';

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
                Tables\Columns\CheckboxColumn::make('validated')
                    ->label('Validation'),
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
                Tables\Columns\TextColumn::make('creneaux_count')->counts("creneaux")
                    ->label('Nombre de créneaux')
                    ->sortable(),
                    ])

            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                TextEntry::make('nom'),
                TextEntry::make('theme'),
                TextEntry::make('description'),
                TextEntry::make('periode'),
                TextEntry::make('membres'),
                TextEntry::make('ambiance'),
                TextEntry::make('nom_resp'),
                TextEntry::make('nom_resp_2'),
                TextEntry::make('mail_resp'),
                TextEntry::make('mail_resp_2'),
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
        ];
    }
}
