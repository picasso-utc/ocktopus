<?php

namespace App\Filament\Public\Resources;

use App\Filament\Public\Resources\ExteResource\Pages;
use App\Filament\Public\Resources\ExteResource\RelationManagers;
use App\Models\Exte;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;


class ExteResource extends Resource
{
    protected static ?string $model = Exte::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-plus';
    protected static ?string $label = 'Mes extés';


    public static function form(Form $form): Form
    {
        $user = session('user');
        return $form
            ->schema([
                Forms\Components\Hidden::make('etu_nom_prenom')->default(MailToName($user->email)),
                Forms\Components\Hidden::make('etu_mail')->default($user->email),
                Forms\Components\TextInput::make('etu_cas')
                    ->label('Quel est ton CAS  ?')
                    ->required(),
                Forms\Components\TextInput::make('exte_nom_prenom')
                    ->label('Quel est le nom et prénom de ton exté ? (1 EXTE MAX)')
                    ->required(),
                Forms\Components\DatePicker::make('exte_date_debut')
                    ->label('Date début')
                    ->helperText("A partir de quelle date viendrait-il ? (au moins une semaine à l'avance)")
                    ->required(),
//                    ->afterOrEqual(Carbon::now()),
                Forms\Components\DatePicker::make('exte_date_fin')
                    ->label('Date fin')
                    ->required()
                    ->helperText("Jusqu'à quelle date viendrait-il )")
                    ->afterOrEqual('exte_date_debut'),
                Forms\Components\Checkbox::make('responsabilite')
                    ->label('En cochant la case ci-dessous, tu prends l\'entière responsabilité des actes de ton exté, et tu certifies ton exté ramènera un document d\'identité physique ainsi qu\'un mail de confirmation venant du Pic\'Asso')
                    ->required(),
                Forms\Components\TextInput::make('commentaire')
                    ->label('Commentaire')
                    ->placeholder('Tu peux nous laisser un très bref commentaire :)')
                    ->maxLength(80)
            ])  ;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')->label('Date de la demande'),
                Tables\Columns\TextColumn::make('etu_nom_prenom')->label('Nom Prénom Étudiant'),
                Tables\Columns\TextColumn::make('etu_mail')->label('Mail Étudiant'),
                Tables\Columns\TextColumn::make('exte_nom_prenom')->label('Nom Prénom Exté'),
                Tables\Columns\TextColumn::make('exte_date_debut')->label('Date début Exté'),
                Tables\Columns\TextColumn::make('exte_date_fin')->label('Date Fin Exté'),
                Tables\Columns\IconColumn::make('mailed')->label('Demande Validée')
                    ->icon(fn($record) => $record->mailed ? 'heroicon-o-check-circle' : 'heroicon-o-clock')->color(
                        fn($record) => $record->mailed? 'success' : 'warning'
                    ),
                ])
            ->filters([
                //
            ])
            ->actions([
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
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
            'index' => Pages\ListExtes::route('/'),
            'create' => Pages\CreateExte::route('/create'),
        ];
    }
}
