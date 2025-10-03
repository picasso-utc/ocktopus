<?php

namespace App\Filament\Admin\Resources;

use App\Enums\AstreinteType;
use App\Filament\Admin\Resources\AstreinteShotgunResource\Pages;
use App\Filament\Admin\Resources\AstreinteShotgunResource\RelationManagers;
use App\Models\Astreinte;
use App\Models\Semestre;
use App\Models\Creneau;
use App\Models\Perm;
use App\Models\User;
use Carbon\Carbon;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class AstreinteShotgunResource extends Resource
{

     /* @var string|null
     */
    protected static ?string $navigationIcon = 'heroicon-o-rocket-launch';


    /**
     * The navigation group under which this resource should be displayed.
     *
     * @var string|null
     */
    protected static ?string $navigationGroup = 'Astreintes';

    /**
     * The label to be used in the navigation menu.
     *
     * @var string|null
     */
    protected static ?string $navigationLabel = 'Shotgun';

    /**
     * The plural label for the resource.
     *
     * @var string|null
     */
    public static ?string $pluralLabel = "Shotgun";

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        $user = session('user');
        $userUuid = $user->uuid;
        $userId =User::where('uuid', $userUuid)->pluck('id')->first();
        return $table
            ->paginated([15]) // 15 éléments par page (3 créneaux x 5 jours)
                ->query(CreneauResource::getEloquentQuery()->whereBetween('date', [self::getStartSemester(), self::getEndSemester()]))
            ->groups([
                Group::make('date')
                    ->date()
                    ->collapsible()
                    ->getDescriptionFromRecordUsing(fn (Creneau $record): string => Carbon::parse($record->date)
                        ->locale('fr')
                        ->translatedFormat('l')),
                ])
            ->defaultGroup('date')
            ->columns([
                Tables\Columns\Layout\Stack::make([
                    Tables\Columns\TextColumn::make('creneau')
                        ->label('creneau')
                        ->state(fn($record) => match ($record->creneau) {
                            'M' => 'Matin',
                            'D' => 'Déjeuner',
                            'S' => 'Soir',
                            'L' => 'Lessive',
                        }),
                    Tables\Columns\TextColumn::make('perm.nom')
                        ->label('Perm associée')
                        ->badge(),
                    Tables\Columns\TextColumn::make('creneau')
                        ->formatStateUsing(function ($state, Creneau $creneau) {

                            $membresDuCreneau = Astreinte::where('creneau_id', $creneau->id)
                                // Jointure interne avec la table users
                                ->join('users', 'astreintes.user_id', '=', 'users.id')
                                // Trier pour obtenir un affichage chronologique cohérent
                                ->orderBy('astreinte_type')
                                ->select('users.email', 'astreintes.astreinte_type') // ajout du second arg pour etre compatible avec MySQL 8+
                                ->distinct()
                                ->get()
                                ->map(function ($user) {
                                    return mailToName($user->email);
                                });

                            return "Astreinteurs : " . $membresDuCreneau->implode(', ');
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
                Filter::make('Semaine actuelle')
                    ->label('Semaine actuelle')
                    ->toggle()
                    ->default()
                    ->query(function (Builder $query) {
                        $query->whereBetween('date', [self::getDateSamediAvant(), self::getDateSamediApres()]);
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('shotgun1')
                    ->label(fn($record) => match ($record->creneau) {
                        'M' => '9h30-10h15',
                        'D' => '11h45-13h',
                        'S' => '17h30-23h',
                    })
                    ->button()
                    ->color(fn($record) => self::determineColor1($record, $userId))
                    ->action(fn($record) => self::handleshotgun1($record, $userId)),
                Tables\Actions\Action::make('shotgun2')
                    ->label(fn($record) => match ($record->creneau) {
                        'M' => '10h-12h',
                        'D' => '12h45h-14h15',
                        'S' => '18h30-23h',
                    })
                    ->button()
                    ->color(fn($record) => self::determineColor2($record, $userId))
                    ->action(fn($record) => self::handleshotgun2($record, $userId)),
                //->disabled(true), -> à creuser pour etre encore mieux
                Tables\Actions\Action::make('lessive')
                    ->label('Lessive')
                    ->button()
                    ->visible(fn($record) =>
                            Carbon::parse($record->date)->isFriday() && $record->creneau === 'S'
                        )
                    ->color(fn($record) => self::determineColorLessive($record, $userId))
                    ->action(fn($record) => self::handleLessive($record, $userId)),
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

    /*****************
     * auxiliaries Functions
     ****************/


    /**
     * Handle the "shotgun 1" action for a slot.
     *
     * @param mixed $record
     * @return void
     */
    private static function handleshotgun1($record, $userId)
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
            $astreinteUser = Astreinte::where('creneau_id', $record->id)
                ->where('user_id', $userId)
                ->where('astreinte_type', $astreinteType)
                ->first();
            $existingAstreinte = Astreinte::where('creneau_id', $record->id)
                    ->where('astreinte_type', $astreinteType)->first() != null;
            if (!$existingAstreinte) {
                $astreinte = new Astreinte([
                    'user_id' =>$userId,
                    'creneau_id' => $record->id,
                    'astreinte_type' => $astreinteType,
                ]);

                // Enregistre l'instance dans la base de données
                $astreinte->save();
            } else {
                if ($astreinteUser) Astreinte::where('creneau_id', $record->id)
                    ->where('user_id', $userId) //A changer
                    ->where('astreinte_type', $astreinteType)
                    ->first()
                    ->delete();
                else Notification::make()
                    ->title('Il n\'y a plus de places pour cette astreinte')
                    ->color('danger')
                    ->send();
            }
        }
    }

    /**
     * Handle the "shotgun 2" action for a slot.
     *
     * @param mixed $record
     * @return void
     */

    private static function handleshotgun2($record , $userId)
    {
        $astreinteType = null;
        $astreinteUserOther = null;
        if ($record->creneau == "M") {
            $astreinteType = "Matin 2";
        } elseif ($record->creneau == "D") {
            $astreinteType = "Déjeuner 2";
        } elseif ($record->creneau == "S") {
            $astreinteType = "Soir 2";
        }
        if ($astreinteType) {
            if ($astreinteType == "Soir 2") {
                $existingAstreinte = Astreinte::where('creneau_id', $record->id)
                        ->where('astreinte_type', $astreinteType)
                        ->count() >= 3;
                $astreinteUser = Astreinte::where('creneau_id', $record->id)
                    ->where('user_id',$userId) //A changer Filament::auth()->id()
                    ->where('astreinte_type', $astreinteType)
                    ->first();
                $astreinteUserOther = Astreinte::where('creneau_id', $record->id)
                    ->where('user_id',$userId) //A changer Filament::auth()->id()
                    ->first();
            } else {
                $existingAstreinte = Astreinte::where('creneau_id', $record->id)
                        ->where('astreinte_type', $astreinteType)->first() != null;
                $astreinteUser = Astreinte::where('creneau_id', $record->id)
                    ->where('user_id', $userId)
                    ->where('astreinte_type', $astreinteType)//A changer Filament::auth()->id()
                    ->first();
            }
            if (!$existingAstreinte && !$astreinteUser && !$astreinteUserOther) {
                $astreinte = new Astreinte([
                    'user_id' => $userId,
                    'creneau_id' => $record->id,
                    'astreinte_type' => $astreinteType,
                ]);
                // Enregistre l'instance dans la base de données
                $astreinte->save();
            } else {
                if ($astreinteUser) {
                    Astreinte::where('creneau_id', $record->id)
                        ->where('user_id', $userId)
                        ->where('astreinte_type', $astreinteType)
                        ->first()
                        ->delete();
                } elseif ($astreinteUserOther) {
                    Notification::make()
                        ->title('Vous avez déjà une perm du soir')
                        ->color('danger')
                        ->send();
                } elseif ($existingAstreinte) {
                    Notification::make()
                        ->title('Il n\'y a plus de places pour cette astreinte')
                        ->color('danger')
                        ->send();
                }
            }
        }
    }

    /**
     * Determine the color for "shotgun 1" action.
     *
     * @param mixed $record
     * @return string|null
     */
    private static function determineColor1($record, $userId)
    {

        if ($record->creneau == "M") {
            $astreinteType = "Matin 1";
        } elseif ($record->creneau == "D") {
            $astreinteType = "Déjeuner 1";
        } elseif ($record->creneau == "S") {
            $astreinteType = "Soir 1";
        }
        if (Astreinte::where('creneau_id', $record->id)
            ->where('user_id', $userId)
            ->where('astreinte_type', $astreinteType)
            ->first())
            return 'success';
        if (Astreinte::where('creneau_id', $record->id)
            ->where('astreinte_type', $astreinteType)->first()) {
            return 'danger';
        }

    }

    /**
     * Determine the color for "shotgun 2" action.
     *
     * @param mixed $record
     * @return string|null
     */
    private static function determineColor2($record, $userId)
    {
        if ($record->creneau == "M") {
            $astreinteType = "Matin 2";
        } elseif ($record->creneau == "D") {
            $astreinteType = "Déjeuner 2";
        } elseif ($record->creneau == "S") {
            $astreinteType = "Soir 2";
        }
        if (Astreinte::where('creneau_id', $record->id)
            ->where('user_id', $userId)
            ->where('astreinte_type', $astreinteType) //A changer Filament::auth()->id()
            ->first()) return 'success';
        if (($astreinteType == "Soir 2" && Astreinte::where('creneau_id', $record->id)->where('astreinte_type', $astreinteType)->count() >= 3) || ($astreinteType != "Soir 2" && Astreinte::where('creneau_id', $record->id)
                    ->where('astreinte_type', $astreinteType)->first())) {
            return 'danger';
        }
    }

    private static function getDateSamediAvant(): Carbon
    {
        $aujourdHui = Carbon::now();

        // Trouver le samedi précédent
        return $aujourdHui->copy()->previous(Carbon::SATURDAY);

    }

    private static function getDateSamediApres(): Carbon
    {
        $aujourdHui = Carbon::now();

        // Trouver le samedi précédent
        return $aujourdHui->copy()->next(Carbon::SATURDAY);
    }


    /**
     * Get the start date of the active semester.
     *
     * @return mixed
     */
    protected static function getStartSemester(): string
    {
        $semestre = Semestre::where('activated', true)->first();

        return $semestre ? $semestre->startOfSemestre : now();
    }

    /**
     * Get the end date of the active semester.
     *
     * @return mixed
     */
    protected static function getEndSemester(): mixed //string ou carbon
    {
        $semestre = Semestre::where('activated', true)->first();

        return $semestre ? $semestre->endOfSemestre : now()->addMonth();
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
            'index' => Pages\ListAstreinteShotguns::route('/'),
        ];
    }

    // Gestion des lessives
    private static function handleLessive($record, $userId)
    {
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

        $existing = Astreinte::where('astreinte_type', AstreinteType::LESSIVE)
            ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
            ->first();

        // Si c'est la bonne personne => désinscription
        if ($existing && $existing->user_id == $userId) {
            $existing->delete();
            return;
        }

        // Si c'est quelqu'un d'autre => impossible
        if ($existing) {
            Notification::make()
                ->title('Le créneau Lessive est déjà pris cette semaine')
                ->color('danger')
                ->send();
            return;
        }

        // Sinon => inscription
        Astreinte::create([
            'user_id' => $userId,
            'creneau_id' => $record->id,
            'astreinte_type' => AstreinteType::LESSIVE,
        ]);
    }


    private static function determineColorLessive($record, $userId)
    {
        $existing = Astreinte::where('astreinte_type', AstreinteType::LESSIVE)
            ->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->first();

        if ($existing && $existing->user_id == $userId) return 'success';
        if ($existing) return 'danger';
    }

}
