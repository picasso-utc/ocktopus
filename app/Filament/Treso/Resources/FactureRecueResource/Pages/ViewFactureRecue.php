<?php

namespace App\Filament\Treso\Resources\FactureRecueResource\Pages;

use App\Filament\Treso\Resources\FactureRecueResource;
use Filament\Actions;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists;

use function Laravel\Prompts\warning;

class ViewFactureRecue extends ViewRecord
{
    protected static string $resource = FactureRecueResource::class;

    protected static ?string $title = "Infos Facture Reçue";

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\EditAction::make()
                ->color('warning'),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema(
                [
                Infolists\Components\Section::make()
                    ->schema(
                        [
                        Infolists\Components\Split::make(
                            [
                            Infolists\Components\Grid::make(2)
                                ->schema(
                                    [
                                    Infolists\Components\Group::make(
                                        [
                                        Infolists\Components\TextEntry::make('facture_number')
                                            ->label('Numéro Facture')
                                            ->color('info'),
                                        ]
                                    ),
                                    Infolists\Components\Group::make(
                                        [
                                        Infolists\Components\TextEntry::make('state')
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
                                        ]
                                    ),
                                    ]
                                )
                            ]
                        )
                        ]
                    ),
                Infolists\Components\Section::make('Détails')
                    ->schema(
                        [
                        Infolists\Components\Fieldset::make('Date')
                            ->schema(
                                [
                                Infolists\Components\TextEntry::make('date')
                                    ->date()
                                    ->columnSpan(1),
                                Infolists\Components\TextEntry::make('date_paiement')
                                    ->date()
                                    ->columnSpan(1),
                                Infolists\Components\TextEntry::make('date_remboursement')
                                    ->date()
                                    ->columnSpan(1),
                                ]
                            )->columns(3),
                        Infolists\Components\Split::make(
                            [
                            Infolists\Components\Grid::make(2)
                                ->schema(
                                    [
                                    Infolists\Components\Group::make(
                                        [
                                        Infolists\Components\TextEntry::make('prix')
                                            ->label('Prix TTC')
                                            ->formatStateUsing(fn (string $state): string => __(number_format($state, 2) . " €")),
                                        ]
                                    ),
                                    Infolists\Components\Group::make(
                                        [
                                        Infolists\Components\Group::make(
                                            [
                                            Infolists\Components\TextEntry::make('tva')
                                                ->label('Tva')
                                                ->formatStateUsing(fn (string $state): string => __(number_format($state, 2) . " €")),
                                            ]
                                        ),
                                        ]
                                    )
                                    ]
                                )
                            ]
                        )
                        ]
                    ),
                Infolists\Components\Section::make('Informations complémentaires')
                    ->schema(
                        [
                        Infolists\Components\Split::make(
                            [
                            Infolists\Components\Grid::make(4)
                                ->schema(
                                    [
                                    Infolists\Components\Group::make(
                                        [
                                        Infolists\Components\TextEntry::make('moyen_paiement')
                                            ->label('Moyen de paiement'),
                                        ]
                                    ),
                                    Infolists\Components\Group::make(
                                        [
                                        IconEntry::make('immobilisation')
                                            ->boolean()
                                        ]
                                    ),
                                    Infolists\Components\TextEntry::make('categoriePrix')
                                        ->label('Catégorie(s)')
                                        ->formatStateUsing(
                                            function ($state) {
                                                return $state->categorie->nom;
                                            }
                                        )
                                        ->color('gray')
                                        ->badge(),
                                    Infolists\Components\TextEntry::make('personne_a_rembourser')
                                        ->label('Personne à rembourser')
                                        ->default('--'),
                                    ]
                                )
                            ]
                        )
                        ]
                    ),
                RepeatableEntry::make('categoriePrix')
                    ->label('Catégorie(s)')
                    ->schema(
                        [
                        Infolists\Components\TextEntry::make('categorie.nom')
                            ->label('Catégorie'),
                        Infolists\Components\TextEntry::make('prix')
                            ->label('Montant de la catégorie')
                            ->formatStateUsing(fn (string $state): string => __(number_format($state, 2) . " €")),
                        ]
                    )->columns(2)->columnSpan('full'),
                ]
            );
    }
}
