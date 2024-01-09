<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\GoodiesResource\Pages;
use App\Filament\Admin\Resources\GoodiesResource\RelationManagers;
use App\Models\Goodies;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class GoodiesResource extends Resource
{
    protected static ?string $model = Goodies::class;
    protected static ?string $label = 'Goodies';
    protected static ?string $navigationGroup = 'General';

    protected static ?string $navigationIcon = 'heroicon-o-gift';

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
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\ToggleColumn::make('collected')
                    ->label('Récupéré')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                //
            ])
            ->bulkActions([
                //
            ])
            ->paginated([20]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageGoodies::route('/'),
        ];
    }
}
