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
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Models\Creneau;



class SemestreResource extends Resource
{
    protected static ?string $model = Semestre::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-path';
    protected static ?string $navigationGroup = 'General';

        /**
     * Define the form for creating and updating semesters.
     *
     * @param Form $form The Filament form instance.
     *
     * @return Form The modified form.
     */
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    /**
     * Define the table columns and configuration for displaying semesters.
     *
     * @param Table $table The Filament table instance.
     *
     * @return Table The modified table.
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\IconColumn::make("activated")
                    ->boolean()
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
                    ->button()
                    ->action(fn($record) => self::handleCreateSemestre($record)),
                Tables\Actions\Action::make('MakeActif')
                    ->label('Rendre actif')
                    ->button()
                    ->action(fn($record) => self::handleMakeActif($record))

            ])

            ->bulkActions([
            ]);
    }

    /**
     * Get the relations associated with this resource.
     *
     * @return array An array of relation configurations.
     */
    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    /**
     * Get the pages associated with this resource.
     *
     * @return array An array of page configurations.
     */
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSemestres::route('/'),
            'create' => Pages\CreateSemestre::route('/create'),
            'edit' => Pages\EditSemestre::route('/{record}/edit'),
        ];
    }


    /*****************
     * auxiliaries Functions
     ****************/

    /**
     * Handle the creation of creneaux for the given semestre.
     *
     * @param mixed $record
     * @return void
     */
    protected static function handleCreateSemestre($record)
    {

        $startDate = Carbon::parse($record->startOfSemestre);
        $endDate = Carbon::parse($record->endOfSemestre);

        $existingCreneau = Creneau::where('date','=', $startDate)->first();

        if (!$existingCreneau) {
            $creneauController = new CreneauController();
            $creneauController->createCreneaux($startDate, $endDate);
            Notification::make()
                ->title('Les créneaux ont bien été crées')
                ->success()
                ->send();
        } else {
            Notification::make()
                ->title('Les créneaux existent déjà')
                ->info()
                ->send();
        }
    }

    /**
     * Make the specified semestre active and deactivate others.
     *
     * @param mixed $record
     * @return void
     */
    public static function handleMakeActif($record) : void
    {
        // Désactiver tous les autres semestres
        Semestre::where('id', '<>', $record->id)->update(['activated' => false]);
        // Activer le semestre actuel
        Semestre::where('id','=',$record->id)->update(['activated'=>true]);
    }



}
