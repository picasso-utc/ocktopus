<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\PermResource\Pages;
use App\Filament\Admin\Resources\PermResource\RelationManagers;
use App\Mail\MailPerm;
use App\Models\Perm;
use App\Models\Semestre;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules\Unique;

class PermResource extends Resource
{
    protected static ?string $model = Perm::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'Gestion des perms';

    protected static ?string $navigationLabel = 'Validations des perms';

    public static function form(Form $form): Form
    {
        $semestreActif = Semestre::where('activated', true)->first();

        return $form
            ->schema([
                Forms\Components\TextInput::make('nom')
                    ->required()
                    ->unique(modifyRuleUsing: function (Unique $rule, callable $get) {
                        return $rule
                            ->where('nom', $get('nom'))
                            ->where('semestre_id', $get('semestre_id'));
                    }, ignoreRecord: true)
                    ->placeholder('Nom de la permanence')
                    ->label('Nom')
                    ->columnSpan([
                        'sm' => 6,
                        'md' => 4,
                        'lg' => 3,
                        'xl' => 3,
                        '2xl' => 3,
                    ]),
                Forms\Components\TextInput::make('theme')
                    ->required()
                    ->placeholder('Thème de la permanence')
                    ->label('Thème')
                    ->columnSpan([
                        'sm' => 6,
                        'md' => 4,
                        'lg' => 3,
                        'xl' => 3,
                        '2xl' => 3,
                    ]),
                Forms\Components\RichEditor::make('description')
                    ->required()
                    ->toolbarButtons(['bold', 'italic', 'underline', 'link', 'unorderedList', 'undo', 'redo'])
                    ->placeholder("Donne nous des détails sur la permanence que vous voulez organiser")
                    ->label('Description (anims du jour et du soir, ambiance, musique)')
                    ->columnSpan([
                        'sm' => 6,
                        'md' => 6,
                        'lg' => 6,
                        'xl' => 6,
                        '2xl' => 6,
                    ]),
                Forms\Components\Placeholder::make('Le Pic est là pour vous !')
                    ->content("N'oubliez pas que la team animation du pic est là pour vous aider à organiser vos anims si besoin ! ")
                    ->columnSpan([
                        'sm' => 6,
                        'md' => 6,
                        'lg' => 6,
                        'xl' => 6,
                        '2xl' => 6,
                    ]),
                Forms\Components\Toggle::make('teddy')
                    ->required()
                    ->label('Habillerez-vous Teddy ?')
                    ->inline(false)
                    ->columnSpan([
                        'sm' => 6,
                        'md' => 6,
                        'lg' => 6,
                        'xl' => 6,
                        '2xl' => 6,
                    ]),
                Forms\Components\Toggle::make('repas')
                    ->required()
                    ->label('Repas prévu ?')
                    ->inline(false)
                    ->reactive()
                    ->columnSpan([
                        'sm' => 6,
                        'md' => 3,
                        'lg' => 3,
                        'xl' => 2,
                        '2xl' => 2,
                    ]),
                Forms\Components\TextInput::make('idea_repas')
                    ->label('Idée du repas')
                    ->placeholder('Des idées du repas ?')
                    ->disabled(fn(callable $get) => !$get('repas'))
                    ->required(fn(callable $get) => $get('repas'))
                    ->columnSpan([
                        'sm' => 6,
                        'md' => 6,
                        'lg' => 4,
                        'xl' => 4,
                        '2xl' => 4,
                    ]),
                Forms\Components\TextInput::make('nom_resp')
                    ->required()
                    ->placeholder('Nom du responsable de la permanence')
                    ->label('Nom du responsable')
                    ->default(mailToName(auth()->user()?->email))
                    ->columnSpan([
                        'sm' => 6,
                        'md' => 4,
                        'lg' => 3,
                        'xl' => 3,
                        '2xl' => 3,
                    ]),
                Forms\Components\TextInput::make('mail_resp')
                    ->required()
                    ->placeholder('Adresse mail du responsable de la permanence')
                    ->label('Adresse mail du responsable')
                    ->default(auth()->user()?->email)
                    ->columnSpan([
                        'sm' => 6,
                        'md' => 4,
                        'lg' => 3,
                        'xl' => 3,
                        '2xl' => 3,
                    ]),
                Forms\Components\TextInput::make('nom_resp_2')
                    ->required()
                    ->placeholder('Nom du sous-responsable')
                    ->label('Nom du sous-responsable')
                    ->columnSpan([
                        'sm' => 6,
                        'md' => 4,
                        'lg' => 3,
                        'xl' => 3,
                        '2xl' => 3,
                    ]),
                Forms\Components\TextInput::make('mail_resp_2')
                    ->required()
                    ->placeholder('Adresse mail du sous-responsable')
                    ->label('Adresse mail du sous-responsable')
                    ->columnSpan([
                        'sm' => 6,
                        'md' => 4,
                        'lg' => 3,
                        'xl' => 3,
                        '2xl' => 3,
                    ]),
                Forms\Components\Toggle::make('asso')
                    ->required()
                    ->label('Géré par une asso ?')
                    ->inline(false)
                    ->reactive()
                    ->columnSpan([
                        'sm' => 6,
                        'md' => 3,
                        'lg' => 3,
                        'xl' => 2,
                        '2xl' => 2,
                    ]),
                Forms\Components\TextInput::make('mail_asso')
                    ->required(fn(Forms\Get $get) => $get('asso'))
                    ->placeholder('Adresse mail de l\'association')
                    ->label('Adresse mail de l\'association')
                    ->columnSpan([
                        'sm' => 6,
                        'md' => 6,
                        'lg' => 4,
                        'xl' => 4,
                        '2xl' => 4,
                    ])
                    ->disabled(fn(Forms\Get $get) => !$get('asso')),
                Forms\Components\TextInput::make('ambiance')
                    ->required()
                    ->label('Ambiance (1 chill - 5 dancefloor endiablé)')
                    ->placeholder('Entre 1 et 5')
                    ->integer()
                    ->minValue(1)
                    ->maxValue(5)
                    ->columnSpan([
                        'sm' => 6,
                        'md' => 3,
                        'lg' => 3,
                        'xl' => 3,
                        '2xl' => 3,
                    ]),
                Forms\Components\TextInput::make('periode')
                    ->required()
                    ->placeholder('Période souhaitée et contraintes')
                    ->label('Période')
                    ->columnSpan([
                        'sm' => 6,
                        'md' => 3,
                        'lg' => 2,
                        'xl' => 2,
                        '2xl' => 3,
                    ]),
                Forms\Components\Select::make('jour')
                    ->multiple()
                    ->options([
                        'lundi' => 'Lundi',
                        'mardi' => 'Mardi',
                        'mercredi' => 'Mercredi',
                        'jeudi' => 'Jeudi',
                        'vendredi' => 'Vendredi',
                    ])
                    ->label('Jours de la semaine préférentiels')
                    ->required()
                    ->columnSpan([
                        'sm' => 6,
                        'md' => 4,
                        'lg' => 3,
                        'xl' => 3,
                        '2xl' => 3,
                    ]),
                Forms\Components\TagsInput::make('membres')
                    ->required()
                    ->placeholder('Appuyez sur Entrée pour ajouter un membre')
                    ->label('Membres de la permanence')
                    ->separator(' - ')
                    ->splitKeys(['Enter', ',', 'Tab'])
                    ->columnSpan([
                        'sm' => 6,
                        'md' => 4,
                        'lg' => 3,
                        'xl' => 3,
                        '2xl' => 3,
                    ]),
                Forms\Components\Toggle::make('artiste')
                    ->required()
                    ->label('Souhaitez-vous accueillir des artistes ?')
                    ->inline(false)
                    ->columnSpan([
                        'sm' => 6,
                        'md' => 6,
                        'lg' => 6,
                        'xl' => 6,
                        '2xl' => 6,
                    ]),
                Forms\Components\TextInput::make('remarques')
                    ->placeholder('Donnez nous les informations qui vous semblent importantes')
                    ->label('Remarque supplémentaires')
                    ->columnSpan([
                        'sm' => 6,
                        'md' => 6,
                        'lg' => 6,
                        'xl' => 6,
                        '2xl' => 6,
                    ]),
                Forms\Components\Hidden::make('semestre_id')->default($semestreActif->id),
            ])
            ->columns(6); // Définit le nombre de colonnes global pour le formulaire
    }


    public static function table(Table $table): Table
    {
        $semestreActif = Semestre::where('activated', true)->first();
        return $table
            ->columns(
                [
                    Tables\Columns\ToggleColumn::make('validated')
                        ->label('Validation'),
                    Tables\Columns\TextColumn::make('nom')
                        ->label('Nom')
                        ->searchable()
                        ->sortable(),
                    Tables\Columns\TextColumn::make('theme')
                        ->label('Thème'),
                    Tables\Columns\IconColumn::make('asso')
                        ->boolean()
                        ->label('Asso')
                        ->sortable(),
                    Tables\Columns\TextColumn::make('creneaux_count')->counts("creneaux")
                        ->label('Nombre de créneaux')
                        ->sortable(),
                ]
            )
            ->filters(
                [
                    SelectFilter::make('semestre_id')
                        ->options(Semestre::all()->pluck('state', 'id'))
                        ->label('Semestre')
                        ->default($semestreActif->id)
                        ->placeholder('Tous les semestre'),
                ]
            )
            ->actions(
                [
                    Tables\Actions\ViewAction::make(),
                    DeleteAction::make()->visible(fn($record) => !$record->validated || $record->semestre_id !== $semestreActif->id),
                    Action::make('sendEmail')
                        ->label('Envoyer Email')
                        ->visible(fn ($record) => !$record->mailed)
                        ->action(function ($record) {
                            self::sendMail($record);
                        }),
                ])
            ->bulkActions(
                [
                    BulkAction::make('envoyer_email')
                        ->label('Envoyer Email')
                        ->action(function (Collection $records) {
                            foreach ($records as $record) {
                                self::sendMail($record);
                            }
                        })
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion(),
                ]
            )
            ->checkIfRecordIsSelectableUsing(
                fn (Model $record): bool => !$record->mailed     )
            ->emptyStateHeading('Aucune permanence');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema(
                [
                    TextEntry::make('nom')
                        ->label('Nom'),
                    TextEntry::make('theme')
                        ->label('Thème'),
                    TextEntry::make('description')
                        ->label('Description')
                        ->html(),
                    TextEntry::make('periode')
                        ->label('Infos sur la période'),
                    TextEntry::make('jour')
                        ->label('Jours souhaités'),
                    TextEntry::make('membres')
                        ->label('Membres'),
                    TextEntry::make('ambiance')
                        ->label('Ambiance '),
                    TextEntry::make('nom_resp')
                        ->label('Responsable n°1'),
                    TextEntry::make('nom_resp_2')
                        ->label('Responsable n°2'),
                    TextEntry::make('mail_resp')
                        ->label('Mail resp n°1'),
                    TextEntry::make('mail_resp_2')
                        ->label('Mail resp n°2'),
                    TextEntry::make('mail_asso')
                        ->label("Mail de l'asso")
                        ->visible(fn($record) => $record->asso)
                        ->copyable(),
                    TextEntry::make('remarques')
                        ->label('Remarques')
                        ->visible(fn($record) => $record->remarques !== null),
                    TextEntry::make('idea_repas')
                        ->label('Repas prévu')
                        ->visible(fn($record) => $record->idea_repas !== null),
                    IconEntry::make('teddy')
                        ->label("Teddy habillé")
                        ->boolean(),
                    IconEntry::make('artiste')
                        ->label("Accueil d'artistes")
                        ->boolean(),
                ]

            );
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
            'index' => Pages\ListPerms::route('/'),
            'create' => Pages\CreatePerm::route('/create'),
        ];
    }


    public static function sendMail($record)
    {
        // Vérification : la perm a-t-elle au moins un créneau ?
        if ($record->creneaux()->count() === 0) {
            \Filament\Notifications\Notification::make()
                ->title('Erreur : La perm n\'est pas dans le planning')
                ->body('Impossible d\'envoyer le mail car aucun créneau n\'est attribué à cette perm.')
                ->danger()
                ->send();
            return;
        }

        DB::beginTransaction();

        try {
            $mailResp1 = $record->mail_resp;
            $mailResp2 = $record->mail_resp_2;
            $mailAsso = $record->mail_asso;

            $email = Mail::to($mailResp1);
            if ($mailResp2 && $mailAsso) {
                $email->cc([$mailResp2, $mailAsso]);
            } elseif ($mailResp2) {
                $email->cc($mailResp2);
            } elseif ($mailAsso) {
                $email->cc($mailAsso);
            }

            /*$nombreCreneaux = $record->nombre_creaneaux;
            if ($nombreCreneaux > 1) {
                $email->send(new MailPerm($record));
            } else {
                $email->send(new MailPerm($record));
            }*/

            //Test
            $nombreCreneaux = $record->creneaux()->count();
            $email->send(new MailPerm($record));

            $record->mailed = 1;
            $record->save();

            DB::commit();

            \Filament\Notifications\Notification::make()
                ->title('Mail envoyé avec succès')
                ->success()
                ->send();
        } catch (Exception $e) {
            DB::rollBack();
            \Filament\Notifications\Notification::make()
                ->title('Erreur lors de l\'envoi du mail')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }
}