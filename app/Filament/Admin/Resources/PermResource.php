<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\PermResource\Pages;
use App\Filament\Admin\Resources\PermResource\RelationManagers;
use App\Models\Perm;
use App\Models\Semestre;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

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
            ->schema(
                [
                Forms\Components\TextInput::make('nom')
                    ->required()
                    ->unique('perms', 'nom')
                    ->placeholder('Nom de la permanence')
                    ->label('Nom')
                    ->columnSpan(4),
                Forms\Components\TextInput::make('theme')
                    ->required()
                    ->placeholder('Thème de la permanence')
                    ->label('Thème')
                    ->columnSpan(2),
                Forms\Components\TextInput::make('nom_resp')
                    ->required()
                    ->placeholder('Nom du responsable de la permanence')
                    ->label('Nom du responsable')
                    //->default(mailToName(auth()->user()?->email))
                    ->columnSpan(3),
                Forms\Components\TextInput::make('mail_resp')
                    ->required()
                    ->placeholder('Adresse mail du responsable de la permanence')
                    ->label('Adresse mail du responsable')
                    //->default(auth()->user()?->email)
                    ->columnSpan(3),
                Forms\Components\TextInput::make('nom_resp_2')
                    ->required()
                    ->placeholder('Nom du sous-responsable')
                    ->label('Nom du sous-responsable')
                    ->columnSpan(3),
                Forms\Components\TextInput::make('mail_resp_2')
                    ->required()
                    ->placeholder('Adresse mail du sous-responsable')
                    ->label('Adresse mail du sous-responsable')
                    ->columnSpan(3),
                Forms\Components\Toggle::make('asso')
                    ->required()
                    ->label('Géré par une asso ?')
                    ->inline(false)
                    ->columnSpan(2)
                    ->reactive(),
                Forms\Components\TextInput::make('mail_asso')
                    ->required(
                        function (Forms\Get $get) {
                            return $get('asso');
                        }
                    )
                    ->placeholder('Adresse mail de l\'association')
                    ->label('Adresse mail de l\'association')
                    ->columnSpan(4)
                    ->disabled(
                        function (Forms\Get $get) {
                            return !$get('asso');
                        }
                    ),
                Forms\Components\RichEditor::make('description')
                    ->required()
                    ->toolbarButtons(['bold', 'italic', 'underline', 'link', 'unorderedList', 'undo', 'redo'])
                    ->placeholder('Description de la permanence')
                    ->label('Description')
                    ->columnSpan(6),
                Forms\Components\TextInput::make('ambiance')
                    ->required()
                    ->label('Ambiance de la perm')
                    ->placeholder('Entre 1 et 5')
                    ->integer()
                    ->minValue(1)
                    ->maxValue(5)
                    ->columnSpan(1),
                Forms\Components\TextInput::make('periode')
                    ->required()
                    ->placeholder('Période souhaitée pour la permanence')
                    ->label('Période')
                    ->columnSpan(3),
                Forms\Components\TagsInput::make('membres')
                    ->required()
                    ->placeholder('Appuyez sur Entrée pour ajouter un membre')
                    ->label('Membres')
                    ->separator(' - ')
                    ->splitKeys(['Enter', ',', 'Tab'])
                    ->columnSpan(3),
                Forms\Components\Hidden::make('semestre')->default($semestreActif->id),

                ]
            )
            ->columns(6);
    }



    public static function table(Table $table): Table
    {
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
                Tables\Columns\TextColumn::make('ambiance')
                    ->label('Ambiance')
                    ->sortable(),
                Tables\Columns\TextColumn::make('periode')
                    ->label('Periode')
                    ->sortable(),
                Tables\Columns\TextColumn::make('creneaux_count')->counts("creneaux")
                    ->label('Nombre de créneaux')
                    ->sortable(),
                ]
            )

            ->filters(
                [
                //
                ]
            )
            ->actions(
                [
                Tables\Actions\ViewAction::make()

                ]
            )
            ->bulkActions(
                [

                ]
            )
            ->emptyStateHeading('Aucune permanence');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema(
                [
                TextEntry::make('nom'),
                TextEntry::make('theme'),
                TextEntry::make('description'),
                TextEntry::make('periode'),
                TextEntry::make('membres'),
                TextEntry::make('ambiance'),
                TextEntry::make('nom_resp'),
                TextEntry::make('nom_resp_2'),
                TextEntry::make('mail_resp'),
                TextEntry::make('mail_resp_2'),
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
}
