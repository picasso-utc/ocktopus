<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CreneauResource\Pages;
use App\Filament\Resources\CreneauResource\RelationManagers;
use App\Models\Creneau;
use App\Models\Perm;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\Indicator;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Grouping\Group;


class CreneauResource extends Resource
{
    protected static ?string $model = Creneau::class;
    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationGroup = 'Gestion des perms';

    protected static ?string $navigationLabel = 'Planning';

    public static ?string $pluralLabel = "Creneaux"; // Modifiez cette ligne

    protected static function getStartSemester()
    {
        $currentDate = now(); // Obtenez la date actuelle
        $currentYear = Carbon::now()->year;
        $semesterStart = null; // Définir des valeurs par défaut

// Déterminez si la date actuelle est dans le semestre d'automne ou de printemps
        if ($currentDate->month >= 7 && $currentDate->day>=15) {
            $semesterStart = Carbon::createFromDate($currentYear, 8, 15);  // 15 août
        }
        elseif ($currentDate->month >= 1 && $currentDate->day <= 20) {
            $semesterStart = Carbon::createFromDate($currentYear, 8, 15);
            $semesterStart->subYear();//retirer une aneee
        }// 15 août
        else {
            $semesterStart =  Carbon::createFromDate($currentYear, 2, 1);   // 1er février
        }
        return $semesterStart;
    }
    protected static function getEndSemester()
    {
        $currentDate = now(); // Obtenez la date actuelle
        $currentYear = Carbon::now()->year;
        $semesterEnd = null; // Définir des valeurs par défaut

// Déterminez si la date actuelle est dans le semestre d'automne ou de printemps
        if ($currentDate->month >= 7 && $currentDate->day>=15) {
            $semesterEnd = Carbon::createFromDate($currentYear, 1, 30);
            $semesterEnd->addYear();// 30 janvier
                    }
        elseif ($currentDate->month >= 1 && $currentDate->day <= 20){
            $semesterEnd = Carbon::createFromDate($currentYear, 1, 30);
        }
        else  {
            $semesterEnd = Carbon::createFromDate($currentYear, 7, 10);    // 10 juillet
        }
        return $semesterEnd;
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
                Group::make('date'),
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
                    })                    ->searchable()
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
                //Tables\Actions\DissociateAction::make('perm')
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
