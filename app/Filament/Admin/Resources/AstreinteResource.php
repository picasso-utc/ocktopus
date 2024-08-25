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
                Tables\Columns\TextColumn::make('creneau.perm.nom')
                    ->label('Perm'),
                Tables\Columns\TextColumn::make('creneau.date')->date()
                    ->label('Date'),
                Tables\Columns\TextColumn::make('astreinte_type')
                    ->formatStateUsing(function ($state) {
                        return match ($state) {
                            'Matin 1' => 'Martin 9h30-10h',
                            'Matin 2' => 'Martin 10h-12h',
                            'Déjeuner 1' => 'Midi 11h45-13h',
                            'Déjeuner 2' => 'Midi 13h-14h30',
                            'Soir 1' => 'Soir 17h30-23h',
                            'Soir 2' => 'Soir 18h30-23h',
                            default => $state,
                        };
                    }),
                ])
            ->filters([
                //
            ])
            ->actions([
            ])
            ->bulkActions([
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
