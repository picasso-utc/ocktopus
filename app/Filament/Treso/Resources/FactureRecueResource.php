<?php

namespace App\Filament\Treso\Resources;

use App\Filament\Treso\Resources\FactureRecueResource\Pages;
use App\Models\CategorieFacture;
use App\Models\FactureRecue;
use App\Models\Semestre;
use Closure;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class FactureRecueResource extends Resource
{
    protected static ?string $model = FactureRecue::class;

    protected static ?string $navigationLabel = 'Factures Reçues';

    protected static ?string $navigationIcon = 'heroicon-o-document-arrow-down';

    protected static ?string $navigationGroup = 'Factures';

    public static function form(Form $form): Form
    {
        $semestreActif = Semestre::where('activated', true)->first();
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
                    ->suffixIcon('heroicon-o-currency-euro')
                    ->rule(function ($get) {
                        return function (string $attribute, $value, Closure $fail) use ($get) {
                            $totalCategoriesPrix = array_sum(array_column($get('categoriePrix') ?? [], 'prix'));
                            if (abs($value - $totalCategoriesPrix) > 0.01) {
                                $fail('Le prix total doit correspondre à la somme des montants des catégories.');
                            }
                        };
                    }),
                
                TextInput::make('tva')
                    ->label('Total TVA (€)')
                    ->required()
                    ->numeric()
                    ->suffixIcon('heroicon-o-currency-euro')
                    ->rule(function ($get) {
                        return function (string $attribute, $value, Closure $fail) use ($get) {
                            $totalCategoriesTVA = array_sum(array_column($get('categoriePrix') ?? [], 'tva'));
                            if (abs($value - $totalCategoriesTVA) > 0.01) {
                                $fail('Le total de la TVA doit correspondre à la somme des TVA des catégories.');
                            }
                        };
                    }),                
                TextInput::make('moyen_paiement')
                    //->label
                    ->required()
                    ->maxLength(255),
                DatePicker::make('date')
                    ->label('Date Facturation')
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
                            ->columnSpan(2),
                        TextInput::make('prix')
                            ->label('Montant de la catégorie')
                            ->numeric()
                            ->suffixIcon('heroicon-o-currency-euro')
                            ->columnSpan(2),
                        TextInput::make('tva')
                            ->label('TVA')
                            ->numeric()
                            ->suffixIcon('heroicon-o-currency-euro')
                            ->columnSpan(2),
                        ]
                    )
                    ->defaultItems(1)
                    ->addActionLabel('Ajouter une catégorie')
                    ->columns(6)
                    ->columnSpan('full'),
                    Select::make('semestre_id')
                        ->label('Semestre')
                        ->options(Semestre::all()->pluck('state', 'id'))
                        ->searchable()
                        ->default($semestreActif->id)
                        ->required()
                        ->columnSpan(6),
                    Hidden::make('facture_number')->default(""),
                ]
            )
            ->columns(6);
    }

    public static function table(Table $table): Table
    {
        $semestreActif = Semestre::where('activated', true)->first();
        return $table
            ->columns(
                [
                TextColumn::make('facture_number')
                    ->label('Réf.')
                    ->searchable()
                    ->color('info'),
                TextColumn::make('destinataire')
                     ->searchable(),
                TextColumn::make('date')
                    ->label('Date Facturation')
                    ->date('M d, Y')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('date_paiement')
                    ->date('M d, Y')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->sortable(),
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
                    ->searchable()
                    ->default('--'),

                TextColumn::make('categoriePrix')
                    ->label('Catégorie(s)')
                    ->formatStateUsing(
                        function ($state) {
                            return $state->categorie->nom;
                        }
                    )
                    ->color('gray')
                    ->badge(),
                ToggleColumn::make('signed')
                    ->label('Signée ?   ')
                    ->onColor('success')
                    ->offColor('danger')
                    ->inline(false),
                ]
            )
            ->filters(
                [
                SelectFilter::make('semestre_id')
                    ->options(Semestre::all()->pluck('state', 'id'))
                    ->label('Semestre')
                    ->default($semestreActif->id)
                    ->placeholder('Tous les semestre'),
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
                    ),
                    Filter::make('categorie')
                        ->form([
                            Select::make('categorie_id')
                                ->label('Catégorie')
                                ->options(
                                    \App\Models\CategorieFacture::pluck('nom', 'id')->toArray() // Récupère les catégories disponibles
                                )
                                ->placeholder('Choisir une catégorie'),
                        ])
                        ->query(function (Builder $query, array $data): Builder {
                            return $query->when(
                                $data['categorie_id'] ?? null, // Vérifie si une catégorie est sélectionnée
                                fn (Builder $query, $categorieId): Builder => $query->whereHas(
                                    'categoriePrix', // Relation dans le modèle FactureRecue
                                    fn (Builder $subQuery) => $subQuery->where('categorie_id', $categorieId)
                                )
                            );
                        }),
                ]
            )
            ->actions(
                [
                    Action::make('ViewPdf')
                        ->url(fn (FactureRecue $record): string => route('image', ['url'=>$record->pdf_path]))
                        ->openUrlInNewTab()
                        ->visible(fn ($record) => $record->pdf_path)
                        ->icon('heroicon-o-eye'),
                    ActionGroup::make(
                        [
                        ViewAction::make(),
                        EditAction::make(),
                        DeleteAction::make(),
                        ]
                    )
                ]
            )
            ->bulkActions(
                [
                /*
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),*/
                ]
            )
            ->defaultSort('date', 'desc');
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
