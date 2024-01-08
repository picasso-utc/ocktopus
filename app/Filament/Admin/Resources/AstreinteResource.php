<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\AstreinteResource\Pages;
use App\Filament\Admin\Resources\AstreinteResource\RelationManagers;
use App\Http\Middleware\Auth;
use App\Models\Astreinte;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use function Webmozart\Assert\Tests\StaticAnalysis\notNull;


class AstreinteResource extends Resource
{
    protected static ?string $model = Astreinte::class;

    protected static ?string $navigationIcon = 'heroicon-o-pencil-square';

    protected static ?string $navigationGroup = 'Gestion des perms';

    protected static ?string $navigationLabel = 'Notation';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\IconColumn::make('note_orga')
                    ->label('Notée')
                    ->boolean(),
                Tables\Columns\TextColumn::make('creneau.perm.nom')
                    ->label('Perm'),
                Tables\Columns\TextColumn::make('creneau.date')->date()
                    ->label('Date'),
                Tables\Columns\TextColumn::make('astreinte_type')
                    ->label('type'),
            ])
            ->filters([
                //
            ])
            ->actions([
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
            'index' => Pages\ListAstreintes::route('/'),
            'test' => Pages\CreateAstreinte::route('/create'),
            'edit' => Pages\EditAstreinte::route('/{record}/edit'),
        ];
    }
}
