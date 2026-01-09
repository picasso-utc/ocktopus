<?php

namespace App\Filament\Public\Resources;

use App\Filament\Public\Resources\SignatureResource\Pages;
use App\Models\Semestre;
use App\Models\SignatureCharte;
use Filament\Forms;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class SignatureResource extends Resource
{
    protected static ?string $model = SignatureCharte::class;

    protected static ?string $navigationGroup = 'Permanences';
    protected static ?string $label = 'Charte Permanencier';
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    public static function form(Form $form): Form
    {
        $user = session('user');
        $userMail = $user?->email;

        // Semestre actif (A25, P26, etc.)
        $semestreActif = Semestre::where('activated', true)->first();
        $semestreId = $semestreActif?->id;

        return $form->schema([
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
                ->rule('accepted'),

            Forms\Components\Hidden::make('adresse_mail')
                ->default($userMail)
                ->required(),

            Forms\Components\Hidden::make('semestre_id')
                ->default($semestreId)
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('created_at')
                ->label("Date de signature")
                ->dateTime('d/m/Y H:i'),

            Tables\Columns\TextColumn::make('adresse_mail')
                ->label("Adresse mail du signataire")
                ->searchable(),

            Tables\Columns\TextColumn::make('semestre.state')
                ->label("Semestre")
                ->sortable(),
        ])
            ->actions([])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListSignatures::route('/'),
            'create' => Pages\CreateSignature::route('/create'),
        ];
    }
}
