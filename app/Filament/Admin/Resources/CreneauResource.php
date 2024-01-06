<?php

namespace App\Filament\Admin\Resources;

use App\Models\Semestre;
use App\Tables\Columns\CreneauAstreinteur;
use App\Filament\Admin\Resources\CreneauResource\Pages;
use App\Filament\Admin\Resources\CreneauResource\RelationManagers;
use App\Models\Astreinte;
use App\Models\Creneau;
use App\Models\Perm;
use App\Models\User;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Grouping\Group;
use Illuminate\View\View;
use function Webmozart\Assert\Tests\StaticAnalysis\null;

/**
 * Resource for managing schedule slots (Créneaux).
 */
class CreneauResource extends Resource
{
    /**
     * The Eloquent model associated with this resource.
     *
     * @var string|null
     */
    protected static ?string $model = Creneau::class;


    /**
     * The icon to be used in the navigation menu.
     *
     * @var string|null
     */
    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    /**
     * The navigation group under which this resource should be displayed.
     *
     * @var string|null
     */
    protected static ?string $navigationGroup = 'Gestion des perms';

    /**
     * The label to be used in the navigation menu.
     *
     * @var string|null
     */
    protected static ?string $navigationLabel = 'Planning';

    /**
     * The plural label for the resource.
     *
     * @var string|null
     */
    public static ?string $pluralLabel = "Planning"; // Modifiez cette ligne


    /**
     * Define the form structure for creating and editing slots. -> UNUSED
     *
     * @param Form $form
     * @return Form
     */
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    /**
     * Define the table structure for listing and managing slots.
     *
     * @param Table $table
     * @return Table
     */
    public static function table(Table $table): Table
    {

        return $table
            ->groups([
                Group::make('date')->date()
                ->collapsible(),
                Group::make('creneau')
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
                            $semestreActifId = Semestre::where('activated', true)->value('id');
                            $perms = Perm::withCount('creneaux')->where('validated', true)->where('semestre',$semestreActifId)->get();
                            $filteredPerms = $perms->filter(function ($perm) {
                                return $perm->creneaux_count < 3;
                            });
                            return $filteredPerms->pluck('nom', 'id')->toArray();
                        })
                        ->placeholder(function ($record) {
                            $associatedPerm = $record->perm;
                            return $associatedPerm ? $associatedPerm->nom : 'Choisir une perm';
                        }),
                    Tables\Columns\TextColumn::make('creneau')
                        ->formatStateUsing(function ($state, Creneau $creneau) {
                            $membresDuCreneau = Astreinte::where('creneau_id', $creneau->id)
                                ->pluck('member_id')
                                ->unique()
                                ->toArray();

                            $emailsMembres = User::whereIn('id', $membresDuCreneau)->pluck('email')
                                ->map(function ($email) {
                                    return mailToName($email);
                                });

                            // Join les emails avec une virgule pour les afficher tous
                            return $emailsMembres->implode(', ');
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
                    ->action(fn($record) => self::dissociatePerm($record)),
                Tables\Actions\Action::make('shotgun1')
                     ->label('shotgun 1')
                        ->button()
                        ->color(fn($record) => self::determineColor1($record))
                        ->action(fn($record) => self::handleshotgun1($record)),
                Tables\Actions\Action::make('shotgun2')
                    ->label('shotgun 2')
                    ->button()
                    ->color(fn($record) => self::determineColor2($record))
                    ->action(fn($record) => self::handleshotgun2($record)),
                    //->disabled(true), -> à creuser pour etre encore mieux
                //Tables\Actions\ViewAction::make()
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

/**
* Get the relations associated with the resource. -> UNUSED
*
* @return array
*/
    public static function getRelations(): array
    {
        return [
            //
        ];
    }


    /**
     * Get the pages associated with the resource.
     *
     * @return array
     */
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCreneaus::route('/'),
            'create' => Pages\CreateCreneau::route('/create'),
            'edit' => Pages\EditCreneau::route('/{record}/edit'),
        ];
    }


    /*****************
     * auxiliaries Functions
     ****************/

    /**
     * Dissociate the associated permission from a specific slot.
     *
     * @param mixed $record
     * @return void
     */
    public static function dissociatePerm($record)
    {
        // Dissocier la perm associée du créneau spécifique
        Creneau::where('id', '=', $record->id)->update(['perm_id' => null]);
    }

    /**
     * Handle the "shotgun 1" action for a slot.
     *
     * @param mixed $record
     * @return void
     */
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
            $astreinteUser = Astreinte::where('creneau_id', $record->id)
                ->where('member_id', 1) //A changer Filament::auth()->id()
                ->where('astreinte_type', $astreinteType)
                ->first();
            if ($astreinteType=="Soir 1"){
                $existingAstreinte = Astreinte::where('creneau_id', $record->id)
                    ->first() !=null;
            }
            else {
                $existingAstreinte = Astreinte::where('creneau_id', $record->id)
                        ->where('astreinte_type', $astreinteType)->first() != null;
            }
            if (!$existingAstreinte) {
                $astreinte = new Astreinte([
                    'member_id' => 1, // À CHANGER
                    'creneau_id' => $record->id,
                    'astreinte_type' => $astreinteType,
                ]);

                // Enregistre l'instance dans la base de données
                $astreinte->save();
            }
            else {
                if ($astreinteUser) Astreinte::where('creneau_id', $record->id)
                    ->where('member_id', 1) //A changer
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

    private static function handleshotgun2($record)
    {
        $astreinteType=null;
        $astreinteUserOther=null;
        if ($record->creneau=="M") {
            $astreinteType = "Matin 2";
        }
        elseif ($record->creneau=="D") {
            $astreinteType = "Déjeuner 2";
        }
        elseif ($record->creneau=="S") {
            $astreinteType = "Soir 2";
        }
        if ($astreinteType) {
            if ($astreinteType== "Soir 2"){
                $existingAstreinte = Astreinte::where('creneau_id', $record->id)->count()>=3;
                $astreinteUser = Astreinte::where('creneau_id', $record->id)
                    ->where('member_id', 1) //A changer Filament::auth()->id()
                    ->where('astreinte_type', $astreinteType)
                    ->first();
                $astreinteUserOther = Astreinte::where('creneau_id', $record->id)
                    ->where('member_id', 1) //A changer Filament::auth()->id()
                    ->first();
            }
            else {
                $existingAstreinte = Astreinte::where('creneau_id', $record->id)
                        ->where('astreinte_type', $astreinteType)->first() != null;
                $astreinteUser = Astreinte::where('creneau_id', $record->id)
                    ->where('member_id', 1)
                    ->where('astreinte_type', $astreinteType)//A changer Filament::auth()->id()
                    ->first();
            }
            if (!$existingAstreinte && !$astreinteUser && !$astreinteUserOther) {
                $astreinte = new Astreinte([
                    'member_id' => 1, // À CHANGER Filament::auth()->id()
                    'creneau_id' => $record->id,
                    'astreinte_type' => $astreinteType,
                ]);
                // Enregistre l'instance dans la base de données
                $astreinte->save();
            }
            else{
                if ($astreinteUser){
                    Astreinte::where('creneau_id', $record->id)
                        ->where('member_id', 1) //A changer Filament::auth()->id()
                        ->where('astreinte_type', $astreinteType)
                        ->first()
                        ->delete();
                }
                elseif ($astreinteUserOther){
                    Notification::make()
                        ->title('Vous avez déjà une perm du soir')
                        ->color('danger')
                        ->send();
                }
                elseif ($existingAstreinte) {
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
    private static function determineColor1($record)
    {
        if ($record->creneau == "M") {
            $astreinteType = "Matin 1";
        } elseif ($record->creneau == "D") {
            $astreinteType = "Déjeuner 1";
        } elseif ($record->creneau == "S") {
            $astreinteType = "Soir 1";
        }
        if (Astreinte::where('creneau_id', $record->id)
            ->where('member_id', 1) //A changer Filament::auth()->id()
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
    private static function determineColor2($record)
    {
        if ($record->creneau == "M") {
            $astreinteType = "Matin 2";
        } elseif ($record->creneau == "D") {
            $astreinteType = "Déjeuner 2";
        } elseif ($record->creneau == "S") {
            $astreinteType = "Soir 2";
        }
        if (Astreinte::where('creneau_id', $record->id)
            ->where('member_id', 1)
            ->where('astreinte_type', $astreinteType) //A changer Filament::auth()->id()
            ->first()) return 'success';
        if (($astreinteType=="Soir 2" && Astreinte::where('creneau_id', $record->id)->count()>=3)||($astreinteType!="Soir 2" && Astreinte::where('creneau_id', $record->id)
            ->where('astreinte_type', $astreinteType)->first())){
            return 'danger';
        }

    }
}


