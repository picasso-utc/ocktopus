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

    protected static function getSemester($date)
    {
        if (($date->month >= 8 && $date->month <= 12) || ($date->month >= 1 && $date->month <= 1)) {
            return 'automne';
        } elseif ($date->month >= 2 && $date->month <= 7) {
            return 'printemps';
        }
        return 'inconnu';
    }

    protected static function getStartSemester()
    {
        $currentDate = now(); // Obtenez la date actuelle
        $currentYear = Carbon::now()->year;
        $semesterStart = null; // Définir des valeurs par défaut

// Déterminez si la date actuelle est dans le semestre d'automne ou de printemps
        $currentSemester = self::getSemester($currentDate);
        if ($currentSemester === 'automne') {
            $semesterStart = Carbon::createFromDate($currentYear, 8, 15);  // 15 août
        } elseif ($currentSemester === 'printemps') {
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
        $currentSemester = self::getSemester($currentDate);
        if ($currentSemester === 'automne') {
            $semesterEnd = Carbon::createFromDate($currentYear, 1, 30);
            $semesterEnd->addYear();// 30 janvier
                    }
        elseif ($currentSemester === 'printemps') {
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
                Group::make('date')
                    ->date(),
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
                        // Récupérez la liste des perms et préparez-la pour la liste déroulante
                        return Perm::pluck('nom', 'id');
                    })
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('perm_id')
                    ->options(Perm::pluck('nom', 'id'))
                    ->label('Par perm')
                    ->placeholder('Toutes les perms'),
                Filter::make('Semestre')
                    ->default(true)
                    ->query(function (Builder $query): Builder {
                        return $query
                            ->when(
                                now()->between(self::getStartSemester(), self::getEndSemester()),
                                fn (Builder $query): Builder => $query
                                    ->whereDate('date', '>=', self::getStartSemester())
                                    ->whereDate('date', '<=', self::getEndSemester()),
                               );
                    })
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
