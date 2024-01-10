<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\BlocagesResource\Pages;
use App\Filament\Admin\Resources\BlocagesResource\RelationManagers;
use App\Models\Blocages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BlocagesResource extends Resource
{
    protected static ?string $model = Blocages::class;
    protected static string $title = 'Utilisateurs bloqués';
    protected static ?string $navigationIcon = 'heroicon-o-user-minus';
    protected static ?string $navigationGroup = "General";

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('cas')
                    ->label('CAS')
                    ->placeholder('CAS du bloqué')
                    ->required(),
                Forms\Components\TextInput::make('reason')
                    ->label('Raison')
                    ->placeholder('Raison du blocage')
                    ->required(),
                Forms\Components\DatePicker::make('date')
                    ->label('Date')
                    ->placeholder('Date du blocage')
                    ->default(now())
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('cas')
                    ->label('CAS')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('reason')
                    ->label('Raison')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('date')
                    ->label('Date')
                    ->formatStateUsing(function ($record) {
                        return $record->date->format('d/m/Y');
                    })
                    ->sortable(),
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
            'index' => Pages\ManageBlocages::route('/'),
        ];
    }
}
