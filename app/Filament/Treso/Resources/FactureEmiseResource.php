<?php

namespace App\Filament\Treso\Resources;

use App\Filament\Treso\Resources\FactureEmiseResource\Pages;
use App\Filament\Treso\Resources\FactureEmiseResource\RelationManagers;
use App\Models\CategorieFacture;
use App\Models\ElementFacture;
use App\Models\FactureEmise;
use App\Models\NoteDeFrais;
use App\Models\Semestre;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Blade;

class FactureEmiseResource extends Resource
{
    protected static ?string $model = FactureEmise::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-arrow-up';

    protected static ?string $navigationGroup = 'Factures';


    public static function form(Form $form): Form
    {
        $semestreActif = Semestre::where('activated', true)->first();
        return $form
            ->schema([
                Fieldset::make('Adresse de facturation')
                    ->schema([
                        TextInput::make('prenom')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('nom')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('numero_voie')
                            ->required()
                            ->numeric(),
                        TextInput::make('rue')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('code_postal')
                            ->required()
                            ->numeric()
                            ->maxLength(5)
                            ->minLength(5),
                        TextInput::make('ville')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->required()
                            ->maxLength(255)
                            ->email()
                            ->suffixIcon('heroicon-m-at-symbol'),
                    ])->columnSpan(6),
                Fieldset::make('Informations')
                    ->schema([
                        DatePicker::make('date_due')
                            ->label('Date dûe')
                            ->required()
                            ->date()
                            ->columnSpan(2)
                            ->timezone('Europe/Paris'),
                        DatePicker::make('date_paiement')
                            ->label('Date paiement')
                            ->date()
                            ->columnSpan(2)
                            ->timezone('Europe/Paris'),
                        Select::make('state')
                            ->label('État')
                            ->required()
                            ->columnSpan(2)
                            ->options([
                                'D' => 'Dûe',
                                'T' => 'Partiellement payée',
                                'P' => 'Payée',
                                'A' => 'Annulée',
                            ]),
                        TextInput::make('destinataire')
                            ->required()
                            ->columnSpan(3)
                            ->maxLength(255),
                        TextInput::make('nom_createur')
                            ->required()
                            ->columnSpan(3)
                            ->maxLength(255),
                    ])->columns(6)->columnSpan(6),
                Repeater::make('elementFacture')
                    ->label('Element(s) de la facture')
                    ->relationship()
                    ->addActionLabel('Ajouter une ligne à la facture')
                    ->schema([
                        TextInput::make('description')
                            ->columnSpan(3)
                            ->required()
                            ->maxLength(255),
                        TextInput::make('quantite')
                            ->label('Quantité')
                            ->required()
                            ->numeric(),
                        TextInput::make('prix_unitaire_ttc')
                            ->label('Prix unitaire TTC')
                            ->required()
                            ->numeric()
                            ->suffixIcon('heroicon-o-currency-euro'),
                        TextInput::make('tva')
                            ->label('TVA (%)')
                            ->required()
                            ->numeric()
                            ->suffixIcon('heroicon-o-currency-euro'),
                    ])
                    ->defaultItems(1)
                    ->columns(6)
                    ->columnSpan(6)
                    ->live()
                    // After adding a new row, we need to update the totals
                    ->afterStateUpdated(function (Get $get, Set $set) {
                        self::updateTotals($get, $set);
                        self::updateTVA($get, $set);
                    })
                    // After deleting a row, we need to update the totals
                    ->deleteAction(
                        fn(Action $action) => $action->after(fn(Get $get, Set $set) => self::updateTotals($get, $set)),
                    )
                    // Disable reordering
                    ->reorderable(false),
                TextInput::make('prix')
                    ->label('Total TTC (€)')
                    ->numeric()
                    ->suffixIcon('heroicon-o-currency-euro')
                    ->columnSpan(3),
                TextInput::make('tva')
                    ->label('Total TVA (€)')
                    ->numeric()
                    ->suffixIcon('heroicon-o-currency-euro')
                    ->columnSpan(3),
                Select::make('semestre_id')
                    ->label('Semestre')
                    ->options(Semestre::all()->pluck('state', 'id'))
                    ->searchable()
                    ->default($semestreActif->id)
                    ->required()
                    ->columnSpan(6)
            ])->columns(6);
    }

    public static function updateTotals(Get $get, Set $set): void
    {
        $selectedProducts = collect($get('elementFacture'))->filter(fn($item) => !empty($item['prix_unitaire_ttc']) && !empty($item['quantite']));

        $total = $selectedProducts->reduce(function ($total, $product) {
            return $total + ($product['prix_unitaire_ttc'] * $product['quantite']);
        }, 0);

        $set('prix', number_format($total, 2, '.', ''));
    }

    public static function updateTVA(Get $get, Set $set): void
    {
        $selectedProducts = collect($get('elementFacture'))->filter(fn($item) => !empty($item['prix_unitaire_ttc']) && !empty($item['quantite']) && !empty($item['tva']));

        $total = $selectedProducts->reduce(function ($total, $product) {
            return $total + ($product['prix_unitaire_ttc'] * $product['quantite'] * $product['tva']/100);
        }, 0);

        $set('tva', number_format($total, 2, '.', ''));
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('destinataire'),
                TextColumn::make('prix')
                    ->label('Prix TTC')
                    ->formatStateUsing(fn (string $state): string => __(number_format($state, 2) . " €"))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('tva')
                    ->label('TVA')
                    ->formatStateUsing(fn (string $state): string => __(number_format($state, 2) . " €"))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('date_due'),
                TextColumn::make('state')
                    ->label('État')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state){
                        'D' => 'Dûe',
                        'T' => 'Partiellement payée',
                        'P' => 'Payée',
                        'A' => 'Annulée',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'D' => 'danger',
                        'T' => 'info',
                        'P' => 'success',
                        'A' => 'grey',
                    }),
                TextColumn::make('nom_createur'),

            ])
            ->filters([
                Tables\Filters\SelectFilter::make('semestre_id')
                        ->options(Semestre::all()->pluck('state', 'id'))
                        ->label('Semestre')
                        ->placeholder('Tous les semestre')
                ]
            )
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('pdf')
                    ->label('PDF')
                    ->color('success')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(function (FactureEmise $record) {
                        return response()->streamDownload(function () use ($record) {
                            echo Pdf::loadHtml(
                                Blade::render('pdfFacture', ['record' => $record])
                            )->stream();
                        }, 'Facture-' . $record->id . '.pdf');
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListFactureEmises::route('/'),
            'create' => Pages\CreateFactureEmise::route('/create'),
            'edit' => Pages\EditFactureEmise::route('/{record}/edit'),
        ];
    }
}
