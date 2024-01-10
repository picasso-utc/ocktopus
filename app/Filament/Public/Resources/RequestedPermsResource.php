<?php

namespace App\Filament\Public\Resources;

use App\Filament\Public\Resources\RequestedPermsResource\Pages;
use App\Filament\Public\Resources\RequestedPermsResource\RelationManagers;
use App\Models\Perm;
use App\Models\RequestedPerms;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RequestedPermsResource extends Resource
{
    use Tables\Concerns\InteractsWithTable;

    protected static ?string $model = Perm::class;
    protected static ?string $navigationGroup = 'Permanences';
    protected static ?string $label = 'Demandes de permanences';
    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    public static function form(Form $form): Form
    {
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
                    ->default(mailToName(auth()->user()?->email))
                    ->columnSpan(3),
                Forms\Components\TextInput::make('mail_resp')
                    ->required()
                    ->placeholder('Adresse mail du responsable de la permanence')
                    ->label('Adresse mail du responsable')
                    ->default(auth()->user()?->email)
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
                Forms\Components\RichEditor::make('description')
                    ->required()
                    ->toolbarButtons(['bold', 'italic', 'underline', 'link', 'unorderedList', 'undo', 'redo'])
                    ->placeholder('Description de la permanence')
                    ->label('Description')
                    ->columnSpan(6),
                Forms\Components\Toggle::make('asso')
                    ->required()
                    ->label('Géré par une asso ?')
                    ->inline(false)
                    ->columnSpan(1)
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
                ]
            )
            ->columns(6);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns(
                [
                Tables\Columns\TextColumn::make('nom')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('theme')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\IconColumn::make('validated')
                    ->label('Validée')
                    ->boolean()
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
                //
                ]
            )
            ->bulkActions(
                [
                //
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
            'index' => Pages\ListRequestedPerms::route('/'),
            'create' => Pages\CreateRequestedPerms::route('/create'),
            'edit' => Pages\EditRequestedPerms::route('/{record}/edit'),
        ];
    }
}
