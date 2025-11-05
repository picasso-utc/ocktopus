<?php

namespace App\Filament\Treso\Resources;

use App\Filament\Treso\Resources\RecetteResource\Pages;
use App\Models\Recettes;
use App\Models\Semestre;
use App\Models\CategorieFacture;
use Filament\Forms\Components\DatePicker;
use Filament\Notifications\Notification;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RecetteResource extends Resource
{
    protected static ?string $model = Recettes::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-euro';

    protected static ?string $navigationGroup = 'Factures';


    public static function form(Form $form): Form
    {
        $semestreActif = Semestre::where('activated', true)->first();
        return $form
            ->schema([
                Select::make('categorie_id')
                    ->label('Catégorie')
                    ->options(CategorieFacture::query()->pluck('nom', 'id'))
                    ->searchable()
                    ->required()
                    ->distinct()
                    ->columnSpan(2),
                TextInput::make('valeur')
                    ->label('Valeur TTC')
                    ->required()
                    ->numeric()
                    ->suffixIcon('heroicon-o-currency-euro')
                    ->columnSpan(2),
                TextInput::make('tva')
                    ->label('TVA (€)')
                    ->numeric()
                    ->suffixIcon('heroicon-o-currency-euro')
                    ->columnSpan(2),
                DatePicker::make('date_debut')
                    ->label('Date début')
                    ->required()
                    ->date()
                    ->columnSpan(2)
                    ->timezone('Europe/Paris'),
                DatePicker::make('date_fin')
                    ->label('Date fin')
                    ->date()
                    ->columnSpan(2)
                    ->timezone('Europe/Paris'),
                TextInput::make('remarque')
                    ->columnSpan(6)
                    ->required()
                    ->maxLength(255),
                Select::make('semestre_id')
                    ->label('Semestre')
                    ->options(Semestre::all()->pluck('state', 'id'))
                    ->searchable()
                    ->default($semestreActif->id)
                    ->required()
                    ->columnSpan(6),
            ])->columns(6);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('categorie_id')
                    ->label('Catégorie')
                    ->formatStateUsing(
                        function ($state) {
                            return CategorieFacture::find($state)->nom;
                        }
                    ),
                TextColumn::make('date_debut')
                    ->date('M d, Y'),
                TextColumn::make('date_fin')
                    ->date('M d, Y'),
                TextColumn::make('valeur')
                    ->label('Valeur TTC')
                    ->formatStateUsing(fn (string $state): string => __(number_format($state, 2) . " €")),
                TextColumn::make('tva')
                    ->label('TVA')
                    ->formatStateUsing(fn (string $state): string => __(number_format($state, 2) . " €")),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('semestre_id')
                        ->options(Semestre::all()->pluck('state', 'id'))
                        ->label('Semestre')
                        ->placeholder('Tous les semestre')
                ]
            )
            ->actions([
                Tables\Actions\Action::make('voir_remarque')
                    ->label('Voir Remarque')
                    ->icon('heroicon-o-eye')
                    ->visible(fn (Recettes $record) => !is_null($record->remarque))
                    ->action(function (Recettes $record, $data) {
                        Notification::make()
                            ->title('Commentaire')
                            ->body($record->commentaire)
                            ->info()
                            ->send();
                    }),
                Tables\Actions\EditAction::make(),
                tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListRecette::route('/'),
            'create' => Pages\CreateRecette::route('/create'),
            'edit' => Pages\EditRecette::route('/{record}/edit'),
        ];
    }
}
