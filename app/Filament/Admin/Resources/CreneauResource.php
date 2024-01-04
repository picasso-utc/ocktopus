<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\CreneauResource\Pages;
use App\Filament\Admin\Resources\CreneauResource\RelationManagers;
use App\Models\Astreinte;
use App\Models\Creneau;
use App\Models\Perm;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Grouping\Group;
use function Webmozart\Assert\Tests\StaticAnalysis\null;


class CreneauResource extends Resource
{
    protected static ?string $model = Creneau::class;
    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationGroup = 'Gestion des perms';

    protected static ?string $navigationLabel = 'Planning';

    public static ?string $pluralLabel = "Creneaux"; // Modifiez cette ligne

    public static function dissociatePerm($record)
    {
        // Dissocier la perm associée du créneau spécifique
        Creneau::where('id', '=', $record->id)->update(['perm_id' => null]);
    }


//    protected static function booted() // MARCHE PAS
//    {
//        static::saving(function ($creneau) {
//            $creneau->week_number = $creneau->date->weekOfYear;
//            $creneau->day_of_week = $creneau->date->format('l'); // 'l' donne le nom du jour de la semaine
//        });
//    }


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
                Tables\Columns\Layout\Stack::make([
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
                                return $perm->creneaux_count <= 3;
                            });
                            return $filteredPerms->pluck('nom', 'id')->toArray();
                        })
                        ->placeholder(function ($record) {
                            $associatedPerm = $record->perm;
                            return $associatedPerm ? $associatedPerm->nom : 'Choisir une perm';
                        }),
                ])
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
                Tables\Actions\Action::make('dissociate')
                    ->label('Libérer')
                    ->button()
                    ->action(fn($record) => self::dissociatePerm($record)),
                Tables\Actions\Action::make('shotgun1')
                     ->label('shotgun 1')
                        ->button()
                        ->color('success')
                        ->action(fn($record) => self::handleshotgun1($record)),
                Tables\Actions\Action::make('shotgun2')
                    ->label('shotgun 2')
                    ->button()
                    ->color('success')
                    ->action(fn($record) => self::handleshotgun2($record)),
                ])

            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                ]),
            ])
            ->contentGrid([
                'sm' => 3,
                'md' => 3,
                'lg' => 3,
                'xl' => 3,
                '2xl' => 3,
            ])
            ->recordUrl(null);
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

    private static function handleshotgun1($record)
    {
        $astreinteType = null;
        if ($record->creneau == "M") {
            $astreinteType = "Matin 1";
        } elseif ($record->creneau == "D") {
            $astreinteType = "Déjeuner 1";
        } elseif ($record->creneau == "S") {
            $astreinteType = "Soir 1";
        }
        if ($astreinteType) {
            if ($astreinteType=="Soir 1"){$existingAstreinte = Astreinte::where('creneau_id', $record->id)
                    ->first() !=null;
            }
            else {
                $existingAstreinte = Astreinte::where('creneau_id', $record->id)
                        ->where('astreinte_type', $astreinteType)->first() != null;
            }
            if (!$existingAstreinte) {
                $astreinte = new Astreinte([
                    'member_id' => 2, // À CHANGER
                    'creneau_id' => $record->id,
                    'astreinte_type' => $astreinteType,
                ]);

                // Enregistre l'instance dans la base de données
                $astreinte->save();
            }
            else {
                Notification::make()
                    ->title('Il n\'y a plus de places pour cette astreinte')
                    ->color('danger')
                    ->send();
            }
        }
    }

        private static function handleshotgun2($record)
    {
        $astreinteType=null;
        if ($record->creneau=="M") {
            $astreinteType = "Matin 2";
        }
        elseif ($record->creneau=="D") {
            $astreinteType = "Déjeuner 2";
        }
        elseif ($record->creneau=="S") {
            $astreinteType = "Soir 2";
        }
        $astreinteUser = false;
        if ($astreinteType) {
            if ($astreinteType== "Soir 2"){
                $existingAstreinte = Astreinte::where('creneau_id', $record->id)->count()>=3;
                $astreinteUser = Astreinte::where('creneau_id', $record->id)
                    ->where('member_id', 2) //A changer
                    ->first();
            }
            else $existingAstreinte = Astreinte::where('creneau_id', $record->id)
                    ->where('astreinte_type', $astreinteType)->first() !=null;

            if (!$existingAstreinte && !$astreinteUser) {
                $astreinte = new Astreinte([
                    'member_id' => 2, // À CHANGER
                    'creneau_id' => $record->id,
                    'astreinte_type' => $astreinteType,
                ]);
                // Enregistre l'instance dans la base de données
                $astreinte->save();
            }
            else{
                if ($existingAstreinte) {
                Notification::make()
                    ->title('Il n\'y a plus de places pour cette astreinte')
                    ->color('danger')
                    ->send();
                }
                if ($astreinteUser){
                    Notification::make()
                        ->title('Vous avez déjà pris une astreinte pour ce créneau')
                        ->color('danger')
                        ->send();
                }
                }
            }
        }
}
