<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\TVLinkResource\Pages;
use App\Filament\Admin\Resources\TVLinkResource\RelationManagers;
use App\Models\Link;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TVLinkResource extends Resource
{
    protected static ?string $model = Link::class;

    protected static ?string $navigationIcon = 'heroicon-o-link';

    protected static ?string $navigationGroup = 'Gestion des télés';

    protected static ?string $navigationLabel = 'Liens télés';

    public static function form(Form $form): Form
    {
        return $form
            ->schema(
                [
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(50),
                Forms\Components\TextInput::make('url')
                    ->required()
                    ->url(),
                ]
            );
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns(
                [
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('url')
                    ->formatStateUsing(
                        function ($state) {
                            return substr($state, 0, 50);
                        }
                    )
                ]
            )
            ->filters(
                [
                //
                ]
            )
            ->actions(
                [
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                ]
            )
            ->bulkActions(
                [
                Tables\Actions\BulkActionGroup::make(
                    [
                    Tables\Actions\DeleteBulkAction::make(),
                    ]
                ),
                ]
            )
            ->emptyStateHeading('Aucun lien de télés');
    }



    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Admin\Resources\TVLinkResource\Pages\ManageTVLinks::route('/'),
        ];
    }
}
