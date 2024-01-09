<?php

namespace App\Filament\Treso\Resources;

use App\Filament\Treso\Resources\CategorieFactureResource\Pages;
use App\Filament\Treso\Resources\CategorieFactureResource\RelationManagers;
use App\Models\Treso\CategorieFacture;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Get;

class CategorieFactureResource extends Resource
{
    protected static ?string $model = CategorieFacture::class;

    protected static ?string $navigationLabel = 'Catégories Facture';

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    protected static ?string $navigationGroup = 'Factures';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                    Grid::make(2)
                        ->schema([
                            Group::make([
                                TextInput::make('nom')
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpan(3),
                                Toggle::make('Sous_catégorie_?')
                                    ->live()
                                    ->columnSpan(3),
                            ]),
                            Group::make([
                                Select::make('parent_id')
                                    ->label('Catégorie parent')
                                    ->options(CategorieFacture::query()->pluck('nom','id'))
                                    ->hidden(fn (Get $get): bool => ! $get('Sous_catégorie_?'))
                                    ->columnSpan(1),
                            ])
                    ])->columns(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nom'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                /*Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),*/
            ])
            ->groups([

                Tables\Grouping\Group::make('parent_id')
                    ->label('Catégorie'),
            ])
            ->defaultGroup('parent_id');
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
            'index' => Pages\ListCategorieFactures::route('/'),
            'create' => Pages\CreateCategorieFacture::route('/create'),
            'edit' => Pages\EditCategorieFacture::route('/{record}/edit'),
        ];
    }

}
