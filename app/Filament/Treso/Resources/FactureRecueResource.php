<?php

namespace App\Filament\Treso\Resources;

use App\Filament\Treso\Resources\FactureRecueResource\Pages;
use App\Models\CategorieFacture;
use App\Models\FactureRecue;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class FactureRecueResource extends Resource
{
    protected static ?string $model = FactureRecue::class;

    protected static ?string $navigationLabel = 'Factures Reçues';

    protected static ?string $navigationIcon = 'heroicon-o-document-arrow-down';

    protected static ?string $navigationGroup = 'Factures';

    public static function form(Form $form): Form
    {
        return $form
            ->schema(
                [
                TextInput::make('destinataire')
                    ->required()
                    ->maxLength(255)
                    ->columnSpan(2),
                TextInput::make('personne_a_rembourser')
                    ->label('Personne à rembourser')
                    ->maxLength(255)
                    ->columnSpan(2),
                TextInput::make('prix')
                    ->label('Prix Total TTC (€)')
                    ->required()
                    ->numeric()
                    ->suffixIcon('heroicon-o-currency-euro'),
                TextInput::make('tva')
                    ->label('TVA (€)')
                    ->required()
                    ->numeric()
                    ->suffixIcon('heroicon-o-currency-euro'),
                TextInput::make('moyen_paiement')
                    //->label
                    ->required()
                    ->maxLength(255),
                DatePicker::make('date')
                    ->required()
                    ->timezone('Europe/Paris'),
                DatePicker::make('date_paiement')
                    ->timezone('Europe/Paris')
                    ->default(null),
                DatePicker::make('date_remboursement')
                    ->timezone('Europe/Paris')
                    ->default(null),
                Select::make('state')
                    ->label('État')
                    ->required()
                    ->options(
                        [
                        'D' => 'Facture à payer',
                        'R' => 'Facture à rembourser',
                        'E' => 'Facture en attente',
                        'P' => 'Facture payée',
                        ]
                    )
                    ->columnSpan(2),
                RichEditor::make('remarque')
                    ->columnSpan(4),
                FileUpload::make('pdf_path')
                    ->label('Importer une facture')
                    ->columnSpan(2)
                    ->previewable(true)
                    ->acceptedFileTypes(['application/pdf','image/jpeg', 'image/png', 'image/gif', 'image/webp']),
                Toggle::make('immobilisation')
                    ->onColor('success'),
                Repeater::make('categoriePrix')
                    ->label('Catégories')
                    ->relationship()
                    ->schema(
                        [
                        Select::make('categorie_id')
                            ->label('Catégorie')
                            ->options(CategorieFacture::query()->pluck('nom', 'id'))
                            /*->createOptionForm([
                                Forms\Components\TextInput::make('nom')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('code')
                                    ->required()
                                    ->maxLength(1),
                            ])*/
                            ->searchable()
                            ->required()
                            ->distinct()
                            ->columnSpan(3),
                        TextInput::make('prix')
                            ->label('Montant de la catégorie')
                            ->numeric()
                            ->suffixIcon('heroicon-o-currency-euro')
                            ->columnSpan(3),
                        ]
                    )
                    ->defaultItems(1)
                    ->addActionLabel('Ajouter une catégorie')
                    ->columns(6)
                    ->columnSpan('full'),
                ]
            )
            ->columns(6);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns(
                [
                TextColumn::make('id')
                    ->label('Réf.')
                    ->formatStateUsing(fn (string $state): string => __("Fact. {$state}"))
                    ->color('info'),
                TextColumn::make('destinataire'),
                TextColumn::make('date')
                    ->date('M d, Y')
                    ->searchable(),
                TextColumn::make('date_paiement')
                    ->date('M d, Y')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('date_remboursement')
                    ->date('M d, Y')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('prix')
                    ->label('Prix TTC')
                    ->searchable()
                    ->formatStateUsing(fn (string $state): string => __(number_format($state, 2) . " €")),
                TextColumn::make('tva')
                    ->formatStateUsing(fn (string $state): string => __(number_format($state, 2) . " €")),
                TextColumn::make('state')
                    ->label('État')
                    ->badge()
                    ->formatStateUsing(
                        fn (string $state): string => match ($state) {
                        'D' => 'Facture à payer',
                        'R' => 'Facture à rembourser',
                        'E' => 'Facture en attente',
                        'P' => 'Facture payée',
                        }
                    )
                    ->color(
                        fn (string $state): string => match ($state) {
                        'D' => 'danger',
                        'R' => 'info',
                        'E' => 'warning',
                        'P' => 'success',
                        }
                    ),
                TextColumn::make('personne_a_rembourser')
                    ->label('Personne à rembourser')
                    ->default('--'),
                TextColumn::make('categoriePrix')
                    ->label('Catégorie(s)')
                    ->searchable()
                    ->formatStateUsing(
                        function ($state) {
                            return $state->categorie->nom;
                        }
                    )
                    ->color('gray')
                    ->badge(),
                ]
            )
            ->filters(
                [
                Filter::make('date')
                    ->form(
                        [
                        DatePicker::make('date_de_début'),
                        DatePicker::make('date_de_fin'),
                        ]
                    )
                    ->query(
                        function (Builder $query, array $data): Builder {
                            return $query
                                ->when(
                                    $data['date_de_début'],
                                    fn (Builder $query, $date): Builder => $query->whereDate('date', '>=', $date),
                                )
                                ->when(
                                    $data['date_de_fin'],
                                    fn (Builder $query, $date): Builder => $query->whereDate('date', '<=', $date),
                                );
                        }
                    )
                ]
            )
            ->actions(
                [
                ActionGroup::make(
                    [
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make(),
                    ]
                ),
                ]
            )
            ->bulkActions(
                [
                /*
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),*/
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
            'index' => Pages\ListFactureRecues::route('/'),
            'create' => Pages\CreateFactureRecue::route('/create'),
            'view' => Pages\ViewFactureRecue::route('/{record}'),
            'edit' => Pages\EditFactureRecue::route('/{record}/edit'),
        ];
    }
}
