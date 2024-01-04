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

    protected static function handleCreateSemestre($record)
    {

        $startDate = Carbon::parse($record->startOfSemestre);
        $endDate = Carbon::parse($record->endOfSemestre);

        $existingCreneau = Creneau::where('date','=', $startDate)->first();

        if (!$existingCreneau) {
            // Si aucun créneau n'existe pour cette date, créez les créneaux
            $creneauController = new CreneauController();
            $creneauController->createCreneaux($startDate, $endDate);
            Notification::make()
                ->title('Les créneaux ont bien été crées')
                ->success()
                ->send();
        } else {
            // Sinon, vous pouvez faire quelque chose, comme afficher un message ou ne rien faire.
            // Vous pouvez également ajouter une logique supplémentaire ici.
            // Par exemple, mise à jour de l'existant, etc.
            // Pour l'exemple, affichons un message.
            Notification::make()
                ->title('Les créneaux existent déjà')
                ->info()
                ->send();
        }
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
            ->contentGrid([
                'md' => 2,
                'lg' => 2,
                '2xl' => 2,
                'sm' => 2,
            ])
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
                    ->button()
                    //->visible(fn (): bool => auth()->user()->hasRole('Admin'))//tester
                    ->action(fn($record) => self::handleCreateSemestre($record)),
                Tables\Actions\Action::make('MakeActif')
                    ->label('Rendre actif')
                    ->button()
                    //->visible(fn (): bool => auth()->user()->hasRole('Admin'))//tester
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
