<?php

namespace App\Filament\Treso\Resources;

use App\Filament\Treso\Resources\ChequeResource\Pages;
use App\Filament\Treso\Resources\ChequeResource\RelationManagers;
use App\Models\Cheque;
use App\Models\FactureRecue;
use Closure;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ChequeResource extends Resource
{
    protected static ?string $model = Cheque::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationGroup = 'Cheques';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('num')
                    ->label('Numéro')
                    ->required()
                    ->numeric()
                    ->maxLength(11)
                    ->columnSpan(2),
                TextInput::make('valeur')
                    ->label('Valeur')
                    ->required()
                    ->numeric()
                    ->default(0.00)
                    ->suffixIcon('heroicon-o-currency-euro')
                    ->columnSpan(2),
                Select::make('state')
                    ->label('État')
                    ->required()
                    ->options(
                        [
                            'E' => 'Encaissé',
                            'A' => 'Annulé',
                            'C' => 'Caution',
                            'P' => 'En Cours',
                        ]
                    )
                    ->columnSpan(2),
                TextInput::make('destinataire')
                    ->label('Destinataire')
                    ->maxLength(255)
                    ->columnSpan(3),
                Select::make('facture_id')
                    ->label('Facture associé')
                    ->options(FactureRecue::all()->pluck('facture_number', 'id'))
                    ->searchable()
                    ->columnSpan(3),
                DatePicker::make('date_encaissement')
                    ->timezone('Europe/Paris')
                    ->default(null)
                    ->columnSpan(3),
                DatePicker::make('date_emission')
                    ->timezone('Europe/Paris')
                    ->default(null)
                    ->columnSpan(3),
                TextInput::make('commentaire')
                    ->label('Commentaire')
                    ->columnSpan(6),
            ])->columns(6);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('num')
                    ->label('Num')
                    ->searchable()
                    ->color('info'),
                TextColumn::make('valeur')
                    ->label('Valeur')
                    ->searchable(),
                TextColumn::make('valeur')
                    ->label('Valeur')
                    ->searchable()
                    ->formatStateUsing(fn (string $state): string => __(number_format($state, 2) . " €")),
                TextColumn::make('state')
                    ->label('État')
                    ->badge()
                    ->searchable()
                    ->formatStateUsing(
                        fn (string $state): string => match ($state) {
                            'E' => 'Encaissé',
                            'A' => 'Annulé',
                            'C' => 'Caution',
                            'P' => 'En Cours',
                        }
                    )
                    ->color(
                        fn (string $state): string => match ($state) {
                            'E' => 'info',
                            'A' => 'danger',
                            'C' => 'warning',
                            'P' => 'success',
                        }
                    ),
                TextColumn::make('destinataire')
                    ->label('Destinataire')
                    ->searchable(),
                TextColumn::make('date_encaissement')
                    ->date('M d, Y')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('date_emission')
                    ->date('M d, Y')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('facture.facture_number')
                    ->label('Facture Associée')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageCheques::route('/'),
        ];
    }
}
