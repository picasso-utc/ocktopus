<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\SemestreResource\Pages;
use App\Filament\Admin\Resources\SemestreResource\RelationManagers;
use App\Http\Controllers\CreneauController;
use App\Models\Semestre;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;



class SemestreResource extends Resource
{
    protected static ?string $model = Semestre::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-path';
    protected static ?string $navigationGroup = 'General';

    protected static function handleCreateSemestre($record)
    {

        $startDate = Carbon::parse($record->startOfSemestre);
        $endDate = Carbon::parse($record->endOfSemestre);
        $creneauController = new CreneauController();
        $creneauController->createCreneaux($startDate, $endDate);
    }

    public static function handleMakeActif($record)
    {
        // Désactiver tous les autres semestres
        Semestre::where('id', '<>', $record->id)->update(['activated' => false]);

        // Activer le semestre actuel
        Semestre::where('id','=',$record->id)->update(['activated'=>true]);
    }

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
                Tables\Columns\BooleanColumn::make("activated")
                    ->label("Actif"),
                Tables\Columns\TextColumn::make("state")
                ->label("Etat"),
                Tables\Columns\TextColumn::make("startOfSemestre")
                    ->label("Début"),
                Tables\Columns\TextColumn::make("endOfSemestre")
                    ->label("Fin"),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('CreateCreneaux')
                    ->label('Créer des créneaux')
                    ->action(fn($record) => self::handleCreateSemestre($record)),
                Tables\Actions\Action::make('MakeActif')
                    ->label('Rendre actif')
                    ->action(fn($record) => self::handleMakeActif($record))
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
            'index' => Pages\ListSemestres::route('/'),
            'create' => Pages\CreateSemestre::route('/create'),
            'edit' => Pages\EditSemestre::route('/{record}/edit'),
        ];
    }
}
