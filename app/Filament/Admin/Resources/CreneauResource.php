<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\CreneauResource\Pages;
use App\Filament\Admin\Resources\CreneauResource\RelationManagers;
use App\Models\Creneau;
use App\Models\Perm;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Grouping\Group;



class CreneauResource extends Resource
{
    protected static ?string $model = Creneau::class;
    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationGroup = 'Gestion des perms';

    protected static ?string $navigationLabel = 'Planning';

    public static ?string $pluralLabel = "Creneaux"; // Modifiez cette ligne

    public static function dissociatePerm(Creneau $creneau)
    {
        // Dissocier la perm associée au créneau spécifique
        $creneau->update(['perm_id' => null]);
    }




    protected static function booted() // MARCHE PAS
    {
        static::saving(function ($creneau) {
            $creneau->week_number = $creneau->date->weekOfYear;
            $creneau->day_of_week = $creneau->date->format('l'); // 'l' donne le nom du jour de la semaine
        });
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
            ->groups([
                Group::make('date')->date()
                ->collapsible()
            ])
            ->defaultGroup('date')
            ->columns([
                Tables\Columns\TextColumn::make('creneau')
                    ->label('creneau'),
                Tables\Columns\TextColumn::make('week_number')
                    ->label('Numéro de semaine')
                    ->sortable(),
                Tables\Columns\SelectColumn::make('perm_id')
                    ->label('Perm')
                    ->options(function () {
                        $perms = Perm::withCount('creneaux')->where('validated', true)  ->get();
                        $filteredPerms = $perms->filter(function ($perm) {
                            return $perm->creneaux_count < 3;
                        });
                        $sortedPerms = $filteredPerms->sortBy('creneaux_count');
                        return $sortedPerms->pluck('nom', 'id')->toArray();
                    })
                    ->placeholder(function ($record) {
                        $associatedPerm = $record->perm;
                        return $associatedPerm ? $associatedPerm->nom : 'Choisir une perm';
                    })
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('perm_id')
                    ->options(Perm::pluck('nom', 'id'))
                    ->label('Par perm')
                    ->placeholder('Toutes les perms'),
                Filter::make('Libre')
                    ->label('Libre')
                    ->query(function (Builder $query) {
                        $query->where('perm_id', null);
                    }),
            ])
            ->actions([
                ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
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
            'index' => Pages\ListCreneaus::route('/'),
            'create' => Pages\CreateCreneau::route('/create'),
            'edit' => Pages\EditCreneau::route('/{record}/edit'),
        ];
    }
}
