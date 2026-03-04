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
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Grouping\Group;
use Illuminate\Database\Eloquent\Model;
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
            ->paginated([15, 30, 45, 60, 'all'])
            ->recordClasses(fn (Model $record) => match ($record->perm_id) {
                'null' => '!opacity-30',
                '6' => '!border-s-2 !border-green-600 !dark:border-green-300',
                default => '!border-s-2 !border-orange-600 dark:border-orange-300',
            })
            ->query(function () {
                return Creneau::query()
                    ->selectRaw('date, MAX(perm_id) as perm_id, MAX(id) as id')
                    ->groupBy('date');
            }) 
            ->groups([
                Group::make('date')
                    ->label('Semaine du semestre')
                    ->getTitleFromRecordUsing(function (Creneau $record) {
                        $debutSemestre = Semestre::where('activated', true)->value('startOfSemestre');
                        $dateCreneau = Carbon::parse($record->date);
                        $startSemestre = Carbon::parse($debutSemestre);
                        $semaineDuSemestre = $startSemestre->diffInWeeks($dateCreneau) + 1;
            
                        return "Semaine $semaineDuSemestre";
                    })
            ])                   
            ->defaultSort('date')          
            ->columns([
                Tables\Columns\Layout\Stack::make([
                    Tables\Columns\TextColumn::make('date')
                        ->label('Date')
                        ->getStateUsing(fn (Creneau $record) => ucfirst(Carbon::parse($record->date)->locale('fr')->translatedFormat('l j F Y')))
                        ->extraAttributes(['class' => 'mb-2'])
                        ->icon('heroicon-o-calendar'),
                    Tables\Columns\TextColumn::make('perm.nom')
                        ->label('Perm')
                        ->formatStateUsing(fn ($state) => "<span style='font-size: 2em;'>{$state}</span>")
                        ->html(),                    
                    Tables\Columns\SelectColumn::make('perm_id')
                        ->label('Associer une perm')
                        ->options(function () {
                            $semestreActifId = Semestre::where('activated', true)->value('id');
                            $perms = Perm::withCount('creneaux')->where('validated', true)->where('semestre_id',$semestreActifId)->get();
                            $filteredPerms = $perms->filter(function ($perm) {
                                return $perm->creneaux_count < 3;
                            });
                            return $filteredPerms->pluck('nom', 'id')->toArray();
                        })
                        ->placeholder("Choisir une perm")
                        ->hidden(function (Creneau $creneau){
                            return $creneau->perm_id;
                        })
                        ->afterStateUpdated(fn ($state, $record) => Creneau::where('date', $record->date)->update(['perm_id' => $state, 'confirmed' => true]))                  
                ])
            ])
            ->filters([
                Filter::make('A venir')
                    ->label('A venir')
                    ->toggle()
                    ->default()
                    ->query(function (Builder $query) {
                        $query->where('date', '>=', Carbon::yesterday());
                    }),
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
                Tables\Actions\Action::make('info_perm')
                    ->label('Infos')
                    ->icon('heroicon-o-information-circle')
                    ->color('info')
                    ->button()
                    ->modalHeading(fn($record) => 'Détails de la permanence (' . ($record->perm->nom ?? '') . ')')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Fermer')
                    ->visible(fn($record) => $record->perm_id !== null)
                    ->infolist([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('perm.nom')
                                    ->label('Nom')
                                    ->visible(fn($record) => !empty($record->perm?->nom)),
                                TextEntry::make('perm.theme')
                                    ->label('Thème')
                                    ->visible(fn($record) => !empty($record->perm?->theme)),
                                TextEntry::make('perm.periode')
                                    ->label('Infos sur la période')
                                    ->visible(fn($record) => !empty($record->perm?->periode)),
                                TextEntry::make('perm.jour')
                                    ->label('Jours souhaités')
                                    ->visible(fn($record) => !empty($record->perm?->jour)),
                                TextEntry::make('perm.ambiance')
                                    ->label('Ambiance')
                                    ->visible(fn($record) => !empty($record->perm?->ambiance)),
                                TextEntry::make('perm.nom_resp')
                                    ->label('Responsable n°1')
                                    ->visible(fn($record) => !empty($record->perm?->nom_resp)),
                                TextEntry::make('perm.nom_resp_2')
                                    ->label('Responsable n°2')
                                    ->visible(fn($record) => !empty($record->perm?->nom_resp_2)),
                                TextEntry::make('perm.mail_resp')
                                    ->label('Mail resp n°1')
                                    ->visible(fn($record) => !empty($record->perm?->mail_resp)),
                                TextEntry::make('perm.mail_resp_2')
                                    ->label('Mail resp n°2')
                                    ->visible(fn($record) => !empty($record->perm?->mail_resp_2)),
                                TextEntry::make('perm.mail_asso')
                                    ->label("Mail de l'asso")
                                    ->visible(fn($record) => !empty($record->perm?->mail_asso)),
                                IconEntry::make('perm.teddy')
                                    ->label("Teddy habillé")->boolean()
                                    ->visible(fn($record) => $record->perm?->teddy !== null),
                                IconEntry::make('perm.artiste')
                                    ->label("Accueil d'artistes")->boolean()
                                    ->visible(fn($record) => $record->perm?->artiste !== null),
                                IconEntry::make('perm.repas')
                                    ->label("Repas prévu")->boolean()
                                    ->visible(fn($record) => $record->perm?->repas !== null),
                                TextEntry::make('perm.idea_repas')
                                    ->label('Idée du repas')
                                    ->visible(fn($record) => !empty($record->perm?->idea_repas)),
                                IconEntry::make('perm.gouter')
                                    ->label("Goûter prévu")->boolean()
                                    ->visible(fn($record) => $record->perm?->gouter !== null),
                                TextEntry::make('perm.idea_gouter')
                                    ->label('Idée du goûter')
                                    ->visible(fn($record) => !empty($record->perm?->idea_gouter)),
                                IconEntry::make('perm.repas_soir')
                                    ->label("Repas prévu (soir)")->boolean()
                                    ->visible(fn($record) => $record->perm?->repas_soir !== null),
                                TextEntry::make('perm.idea_repas_soir')
                                    ->label('Idée du repas (soir)')
                                    ->visible(fn($record) => !empty($record->perm?->idea_repas_soir)),
                                TextEntry::make('perm.description')
                                    ->label('Description')->html()
                                    ->visible(fn($record) => !empty($record->perm?->description))
                                    ->columnSpanFull(),
                                TextEntry::make('perm.membres')
                                    ->label('Membres')
                                    ->visible(fn($record) => !empty($record->perm?->membres))
                                    ->columnSpanFull(),
                                TextEntry::make('perm.remarques')
                                    ->label('Remarques')
                                    ->visible(fn($record) => !empty($record->perm?->remarques))
                                    ->columnSpanFull(),
                            ]),
                    ]),
                Tables\Actions\Action::make('dissociate')
                    ->label('Supprimer perm')
                    ->color("danger")
                    ->button()
                    ->visible(fn($record) => $record->perm_id !== null)
                    ->action(fn ($record) => Creneau::where('date', $record->date)->update(['perm_id' => null, 'confirmed' => false])) ,                             
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
}