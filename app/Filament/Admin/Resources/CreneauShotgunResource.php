<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\CreneauShotgunResource\Pages;
use App\Filament\Admin\Resources\CreneauShotgunResource\RelationManagers;
use App\Models\Astreinte;
use App\Models\Creneau;
use App\Models\CreneauShotgun;
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

class CreneauShotgunResource extends Resource
{
    /**
     * The Eloquent model associated with this resource.
     *
     * @var string|null
     */
    protected static ?string $model = Creneau::class;

    /**
     * The Eloquent model associated with this resource.
     *
     * @var string|null
     */
    protected static ?string $navigationIcon = 'heroicon-o-rocket-launch';


    /**
     * The navigation group under which this resource should be displayed.
     *
     * @var string|null
     */
    protected static ?string $navigationGroup = '';

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

        $userUuid = 1; //Filament::auth()->id()
        return $table
            ->query(CreneauResource::getEloquentQuery()->whereBetween('date', [self::getDateSamediAvant(), self::getDateSamediApres()]))
            ->groups([
                Group::make('date')->date()
                    ->collapsible()
                    ->getDescriptionFromRecordUsing(fn(Creneau $record): string => Carbon::parse($record->date)->format('l')),
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
                        }),
                    Tables\Columns\TextColumn::make('perm.nom')
                        ->label('Perm associée')
                        ->badge(),
                    Tables\Columns\TextColumn::make('creneau')
                        ->formatStateUsing(function ($state, Creneau $creneau) {
                            $membresDuCreneau = Astreinte::where('creneau_id', $creneau->id)
                                ->pluck('member_id')
                                ->unique()
                                ->toArray();
                            $nomsMembres = User::whereIn('id', $membresDuCreneau)->pluck('email')
                                ->map(function ($email) {
                                    return mailToName($email);
                                });

                            // Join les emails avec une virgule pour les afficher tous
                            return "Astreinteurs : " . $nomsMembres->implode(', ');
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
                Tables\Actions\Action::make('shotgun1')
                    ->label(fn($record) => match ($record->creneau) {
                        'M' => '9h30-10h',
                        'D' => '11h45-13h',
                        'S' => '17h30-23h',
                    })
                    ->button()
                    ->color(fn($record) => self::determineColor1($record, $userUuid))
                    ->action(fn($record) => self::handleshotgun1($record, $userUuid)),
                Tables\Actions\Action::make('shotgun2')
                    ->label(fn($record) => match ($record->creneau) {
                        'M' => '10h-12h',
                        'D' => '13h-14h30',
                        'S' => '18h30-23h',
                    })
                    ->button()
                    ->color(fn($record) => self::determineColor2($record, $userUuid))
                    ->action(fn($record) => self::handleshotgun2($record, $userUuid)),
                //->disabled(true), -> à creuser pour etre encore mieux
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
    private static function handleshotgun1($record, $userUuid)
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
                ->where('member_id', $userUuid)
                ->where('astreinte_type', $astreinteType)
                ->first();
            if ($astreinteType == "Soir 1") {
                $existingAstreinte = Astreinte::where('creneau_id', $record->id)
                        ->first() != null;
            } else {
                $existingAstreinte = Astreinte::where('creneau_id', $record->id)
                        ->where('astreinte_type', $astreinteType)->first() != null;
            }
            if (!$existingAstreinte) {
                $astreinte = new Astreinte([
                    'member_id' =>$userUuid,
                    'creneau_id' => $record->id,
                    'astreinte_type' => $astreinteType,
                ]);

                // Enregistre l'instance dans la base de données
                $astreinte->save();
            } else {
                if ($astreinteUser) Astreinte::where('creneau_id', $record->id)
                    ->where('member_id', $userUuid) //A changer
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

    private static function handleshotgun2($record , $userUuid)
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
                $existingAstreinte = Astreinte::where('creneau_id', $record->id)->count() >= 3;
                $astreinteUser = Astreinte::where('creneau_id', $record->id)
                    ->where('member_id',$userUuid) //A changer Filament::auth()->id()
                    ->where('astreinte_type', $astreinteType)
                    ->first();
                $astreinteUserOther = Astreinte::where('creneau_id', $record->id)
                    ->where('member_id',$userUuid) //A changer Filament::auth()->id()
                    ->first();
            } else {
                $existingAstreinte = Astreinte::where('creneau_id', $record->id)
                        ->where('astreinte_type', $astreinteType)->first() != null;
                $astreinteUser = Astreinte::where('creneau_id', $record->id)
                    ->where('member_id', $userUuid)
                    ->where('astreinte_type', $astreinteType)//A changer Filament::auth()->id()
                    ->first();
            }
            if (!$existingAstreinte && !$astreinteUser && !$astreinteUserOther) {
                $astreinte = new Astreinte([
                    'member_id' => $userUuid,
                    'creneau_id' => $record->id,
                    'astreinte_type' => $astreinteType,
                ]);
                // Enregistre l'instance dans la base de données
                $astreinte->save();
            } else {
                if ($astreinteUser) {
                    Astreinte::where('creneau_id', $record->id)
                        ->where('member_id', $userUuid)
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
    private static function determineColor1($record, $userUuid)
    {

        if ($record->creneau == "M") {
            $astreinteType = "Matin 1";
        } elseif ($record->creneau == "D") {
            $astreinteType = "Déjeuner 1";
        } elseif ($record->creneau == "S") {
            $astreinteType = "Soir 1";
        }
        if (Astreinte::where('creneau_id', $record->id)
            ->where('member_id', $userUuid)
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
    private static function determineColor2($record, $userUuid)
    {
        if ($record->creneau == "M") {
            $astreinteType = "Matin 2";
        } elseif ($record->creneau == "D") {
            $astreinteType = "Déjeuner 2";
        } elseif ($record->creneau == "S") {
            $astreinteType = "Soir 2";
        }
        if (Astreinte::where('creneau_id', $record->id)
            ->where('member_id', $userUuid)
            ->where('astreinte_type', $astreinteType) //A changer Filament::auth()->id()
            ->first()) return 'success';
        if (($astreinteType == "Soir 2" && Astreinte::where('creneau_id', $record->id)->count() >= 3) || ($astreinteType != "Soir 2" && Astreinte::where('creneau_id', $record->id)
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCreneauShotguns::route('/'),
            'create' => Pages\CreateCreneauShotgun::route('/create'),
            'edit' => Pages\EditCreneauShotgun::route('/{record}/edit'),
        ];
    }
}
