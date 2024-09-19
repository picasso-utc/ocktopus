<?php

namespace App\Filament\Public\Resources;

use App\Filament\Public\Resources\SignatureResource\Pages;
use App\Filament\Public\Resources\SignatureResource\RelationManagers;
use App\Models\Creneau;
use App\Models\Signature;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Placeholder;
use Illuminate\Support\HtmlString;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SignatureResource extends Resource
{
    protected static ?string $model = Signature::class;
    protected static ?string $navigationGroup = 'Permanences';
    protected static ?string $label = 'Signatures';
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    public static function form(Form $form): Form
    {
        $user = session('user');
        $userMail = $user->email;

        $creneauToday = Creneau::where('date', now()->format('Y-m-d'))->first();
        $perm_id = $creneauToday->perm_id;
        return $form
        ->schema([
            Placeholder::make('charte')
                ->label('')
                ->content(new HtmlString('
                    <p><strong>Ce document est valable pour toute la durée du semestre en cours et sera susceptible d’être utilisé comme justificatif de
                    responsabilité de l’étudiant en cas de dégradation ou de comportement inapproprié lors d’une de ses permanences dans le foyer étudiant.</strong></p>
                    <p>Je soussigné membre de l’association / membre du groupe, engage ma personne et le reste de mon équipe à respecter les règles suivantes lors de toutes les permanences tenues au Pic’asso durant le semestre :</p>
                    <ul>
                        <li>- Respecter les décisions de l’équipe d’astreinte.</li>
                        <li>- Respecter le matériel mis à disposition des permanenciers.</li>
                        <li>- Vérifier le pass vaccinal de toutes les personnes entrant dans l’enceinte du foyer.</li>
                        <li>- Payer mes consommations pendant mes permanences.</li>
                        <li>- Ne pas servir une personne qui a trop bu.</li>
                        <li>- Distribuer des éthylotests aux conducteurs.</li>
                        <li>- Rester sobre pendant la permanence.</li>
                        <li>- Effectuer les tâches ménagères correctement.</li>
                    </ul>
                    <p>La caution est un chèque de 200€ à l’ordre du BDE UTC Pic’asso. Le non-respect de ces règles peut entraîner l’encaissement de la caution, totale ou partielle selon la gravité.</p>
                '))
                ->columnSpan('full'),


                Checkbox::make('agree')
                    ->label("Je certifie être en accord avec la charte du permanencier")
                    ->required()
                    ->rule('accepted'), // Validation pour s'assurer que la case est cochée
                Forms\Components\Hidden::make('adresse_mail')->default($userMail),
                Forms\Components\Hidden::make('perm_id')->default($perm_id)

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label("Date de signature"),
                Tables\Columns\TextColumn::make('adresse_mail')
                    ->label("Adresse mail du signataire"),
                Tables\Columns\TextColumn::make('perm.nom')
            ])
            ->filters([
                //
            ])
            ->actions([
            ])
            ->bulkActions([
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
            'index' => Pages\ListSignatures::route('/'),
            'create' => Pages\CreateSignature::route('/create'),
        ];
    }
}
