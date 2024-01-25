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
use Illuminate\Support\Facades\Auth;
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
                ->collapsible()
                ->getDescriptionFromRecordUsing(fn (Creneau $record): string => Carbon::parse($record->date)->format('l')),

                Group::make('creneau')
            ])
            ->defaultGroup('date')
            ->columns([
                Tables\Columns\Layout\Stack::make([
                    Tables\Columns\TextColumn::make('creneau')
                        ->label('dd')
                        ->state(fn ($record) => match ($record->creneau) {
                            'M' => 'Matin',
                            'D' => 'Déjeuner',
                            'S' => 'Soir',
                        }),
                    Tables\Columns\TextColumn::make('perm.nom')
                        ->label('Perm associée')
                        ->badge(),
                    Tables\Columns\SelectColumn::make('perm_id')
                        ->label('Associer une perm')
                        ->options(function () {
                            $semestreActifId = Semestre::where('activated', true)->value('id');
                            $perms = Perm::withCount('creneaux')->where('validated', true)->where('semestre',$semestreActifId)->get();
                            $filteredPerms = $perms->filter(function ($perm) {
                                return $perm->creneaux_count < 3;
                            });
                            return $filteredPerms->pluck('nom', 'id')->toArray();
                        })
                        ->placeholder("Choisir une perm")
                        //(function ($record) {
//                            $associatedPerm = $record->perm;
//                            return $associatedPerm ? $associatedPerm->nom : 'Choisir une perm';
//                        })
                        ->hidden(function (Creneau $creneau){
                            return $creneau->perm_id;
                        }),
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
                Tables\Actions\Action::make('dissociate')
                    ->label('Libérer')
                    ->button()
                    ->action(fn($record) => self::dissociatePerm($record)),
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

}


