<?php

namespace App\Filament\Treso\Resources;

use App\Filament\Treso\Resources\NoteDeFraisResource\Pages;
use App\Filament\Treso\Resources\NoteDeFraisResource\RelationManagers;
use App\Models\CategorieFacture;
use App\Models\ElementNoteDeFrais;
use App\Models\NoteDeFrais;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Forms\Components\Actions\Action;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Blade;

class NoteDeFraisResource extends Resource
{
    protected static ?string $model = NoteDeFrais::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Fieldset::make('Coordonnées')
                    ->schema([
                        TextInput::make('prenom')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('nom')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('numero_voie')
                            ->numeric(),
                        TextInput::make('rue')
                            ->maxLength(255),
                        TextInput::make('code_postal')
                            ->numeric(),
                        TextInput::make('ville')
                            ->maxLength(255),
                        TextInput::make('email')
                            ->required()
                            ->maxLength(255)
                            ->email()
                            ->suffixIcon('heroicon-m-at-symbol'),
                    ]),
                Fieldset::make('Informations')
                    ->schema([
                        DatePicker::make('date_facturation')
                            ->label('Date de facturation')
                            ->required()
                            ->date()
                            ->timezone('Europe/Paris'),
                        Select::make('state')
                            ->label('État')
                            ->required()
                            ->options([
                                'D' => 'Note à payer',
                                'R' => 'Note à rembourser',
                                'E' => 'Note en attente',
                                'P' => 'Note payée',
                            ])
                    ]),
                Repeater::make('elementNote')
                    ->label('Element(s) de la note')
                    ->relationship()
                    ->addActionLabel('Ajouter une ligne à la note')
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
                    ->columnSpan('full')
                    ->live()
                    // After adding a new row, we need to update the totals
                    ->afterStateUpdated(function (Get $get, Set $set) {
                        self::updateTotals($get, $set);
                    })
                    // After deleting a row, we need to update the totals
                    ->deleteAction(
                        fn(Action $action) => $action->after(fn(Get $get, Set $set) => self::updateTotals($get, $set)),
                    )
                    // Disable reordering
                    ->reorderable(false),
                TextInput::make('total')
                    ->label('Total TTC (€)')
                    ->numeric()
                    ->suffixIcon('heroicon-o-currency-euro'),
            ]);
    }

    public static function updateTotals(Get $get, Set $set): void
    {
        $selectedProducts = collect($get('elementNote'))->filter(fn($item) => !empty($item['prix_unitaire_ttc']) && !empty($item['quantite']));

        $total = $selectedProducts->reduce(function ($total, $product) {
            return $total + ($product['prix_unitaire_ttc'] * $product['quantite']);
        }, 0);

        $set('total', number_format($total, 2, '.', ''));
    }



    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('prenom'),
                TextColumn::make('nom'),
                TextColumn::make('date_facturation'),
                TextColumn::make('state')
                    ->label('État')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state){
                        'D' => 'Note à payer',
                        'R' => 'Note à rembourser',
                        'E' => 'Note en attente',
                        'P' => 'Note payée',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'D' => 'danger',
                        'R' => 'info',
                        'E' => 'warning',
                        'P' => 'success',
                    }),
                /*TextColumn::make('total')
                    ->label('Total TTC')
                    ->formatStateUsing(fn (string $state): string => __(number_format($state, 2) . " €")),*/
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('pdf')
                    ->label('PDF')
                    ->color('success')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(function (NoteDeFrais $record) {
                        return response()->streamDownload(function () use ($record) {
                            echo Pdf::loadHtml(
                                Blade::render('pdfNote', ['record' => $record])
                            )->stream();
                        }, 'Note-' . $record->id . '.pdf');
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListNoteDeFrais::route('/'),
            'create' => Pages\CreateNoteDeFrais::route('/create'),
            'edit' => Pages\EditNoteDeFrais::route('/{record}/edit'),
        ];
    }
}
