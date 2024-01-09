<?php

namespace App\Filament\Admin\Widgets;

use App\Filament\Admin\Resources\CreneauResource;
use App\Models\Astreinte;
use App\Models\Creneau;
use App\Models\Perm;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class ShotgunCreneau extends BaseWidget
{
    protected static ?int $sort = 4;
    protected int | string | array $columnSpan = 'full';
    protected static ?string $maxWeight = '300px';

    public function table(Table $table): Table
    {
        return $table
            ->query(CreneauResource::getEloquentQuery()->whereBetween('date', [self::getDateSamediAvant(), self::getDateSamediApres()]))
            ->groups(
                [
                Group::make('date')->date()
                    ->collapsible()
                    ->getDescriptionFromRecordUsing(fn (Creneau $record): string => Carbon::parse($record->date)->format('l')),
                ]
            )
            ->defaultGroup('date')
            ->columns(
                [
                Tables\Columns\Layout\Stack::make(
                    [
                    Tables\Columns\TextColumn::make('creneau')
                        ->label('creneau'),
                    Tables\Columns\TextColumn::make('perm.nom')
                        ->label('Perm associée')
                        ->badge(),
                    Tables\Columns\TextColumn::make('creneau')
                        ->formatStateUsing(
                            function ($state, Creneau $creneau) {
                                $membresDuCreneau = Astreinte::where('creneau_id', $creneau->id)
                                ->pluck('member_id')
                                ->unique()
                                ->toArray();

                                $nomsMembres = User::whereIn('id', $membresDuCreneau)->pluck('email')
                                    ->map(
                                        function ($email) {
                                            return mailToName($email);
                                        }
                                    );

                                // Join les emails avec une virgule pour les afficher tous
                                return "Astreinteurs : " . $nomsMembres->implode(', ');
                            }
                        ),
                    ]
                )
                ]
            )
            ->filters(
                [
                Tables\Filters\SelectFilter::make('perm_id')
                    ->options(Perm::pluck('nom', 'id'))
                    ->label('Par perm')
                    ->placeholder('Toutes les perms'),
                Filter::make('Libre')
                    ->label('Libre')
                    ->query(
                        function (Builder $query) {
                            $query->where('perm_id', null);
                        }
                    ),
                ]
            )

            ->actions(
                [
                Tables\Actions\Action::make('shotgun1')
                    ->label(
                        fn($record) => match ($record->creneau) {
                        'M' => '9h30-10h',
                        'D' => '11h45-13h',
                        'S' => '17h30-23h',
                        }
                    )
                    ->button()
                    ->color(fn($record) => self::determineColor1($record))
                    ->action(fn($record) => self::handleShotgun1($record)),
                Tables\Actions\Action::make('shotgun2')
                    ->label(
                        fn($record) => match ($record->creneau) {
                        'M' => '10h-12h',
                        'D' => '13h-14h30',
                        'S' => '18h30-23h',
                        }
                    )
                    ->button()
                    ->color(fn($record) => self::determineColor2($record))
                    ->action(fn($record) => self::handleShotgun2($record)),
                //->disabled(true), -> à creuser pour etre encore mieux
                ]
            )

            ->bulkActions(
                [
                Tables\Actions\BulkActionGroup::make(
                    [
                    ]
                ),
                ]
            )
            ->contentGrid(
                [
                'sm' => 3,
                'md' => 3,
                'lg' => 3,
                'xl' => 3,
                '2xl' => 3,
                ]
            )
            ->recordUrl(null);
    }

    /**
     * Handle the "shotgun 1" action for a slot.
     *
     * @param  mixed $record
     * @return void
     */
    private static function handleShotgun1($record): void
    {
        $astreinteType = null;
        if ($record->creneau == "M") {
            $astreinteType = "Matin 1";
        } elseif ($record->creneau == "D") {
            $astreinteType = "Déjeuner 1";
        } elseif ($record->creneau == "S") {
            $astreinteType = "Soir 1";
        }
        if ($astreinteType) {
            $astreinteUser = Astreinte::where('creneau_id', $record->id)
                ->where('member_id', 1) //A changer Filament::auth()->id()
                ->where('astreinte_type', $astreinteType)
                ->first();
            if ($astreinteType == "Soir 1") {
                $existingAstreinte = Astreinte::where('creneau_id', $record->id)
                        ->first() != null;
            } else {
                $existingAstreinte = Astreinte::where('creneau_id', $record->id)
                        ->where('astreinte_type', $astreinteType)->first() != null;
            }
            if (!$existingAstreinte) {
                $astreinte = new Astreinte(
                    [
                    'member_id' => 1, // À CHANGER
                    'creneau_id' => $record->id,
                    'astreinte_type' => $astreinteType,
                    ]
                );

                // Enregistre l'instance dans la base de données
                $astreinte->save();
            } else {
                if ($astreinteUser) {
                    Astreinte::where('creneau_id', $record->id)
                    ->where('member_id', 1) //A changer
                    ->where('astreinte_type', $astreinteType)
                    ->first()
                    ->delete();
                } else {
                    Notification::make()
                    ->title('Il n\'y a plus de places pour cette astreinte')
                    ->color('danger')
                    ->send();
                }
            }
        }
    }

    /**
     * Handle the "shotgun 2" action for a slot.
     *
     * @param  mixed $record
     * @return void
     */
    private static function handleShotgun2(mixed $record): void
    {
        $astreinteType = null;
        $astreinteUserOther = null;
        if ($record->creneau == "M") {
            $astreinteType = "Matin 2";
        } elseif ($record->creneau == "D") {
            $astreinteType = "Déjeuner 2";
        } elseif ($record->creneau == "S") {
            $astreinteType = "Soir 2";
        }
        if ($astreinteType) {
            if ($astreinteType == "Soir 2") {
                $existingAstreinte = Astreinte::where('creneau_id', $record->id)->count() >= 3;
                $astreinteUser = Astreinte::where('creneau_id', $record->id)
                    ->where('member_id', Filament::auth()->id())
                    ->where('astreinte_type', $astreinteType)
                    ->first();
                $astreinteUserOther = Astreinte::where('creneau_id', $record->id)
                    ->where('member_id', Filament::auth()->id())
                    ->first();
            } else {
                $existingAstreinte = Astreinte::where('creneau_id', $record->id)
                        ->where('astreinte_type', $astreinteType)->first() != null;
                $astreinteUser = Astreinte::where('creneau_id', $record->id)
                    ->where('member_id', Filament::auth()->id())
                    ->where('astreinte_type', $astreinteType)
                    ->first();
            }

            if (!$existingAstreinte && !$astreinteUser && !$astreinteUserOther) {
                $astreinte = new Astreinte(
                    [
                    'member_id' => Filament::auth()->id(),
                    'creneau_id' => $record->id,
                    'astreinte_type' => $astreinteType,
                    ]
                );
                $astreinte->save();
            } else {
                if ($astreinteUser) {
                    Astreinte::where('creneau_id', $record->id)
                        ->where('member_id', 1) //A changer Filament::auth()->id()
                        ->where('astreinte_type', $astreinteType)
                        ->first()
                        ->delete();
                } elseif ($astreinteUserOther) {
                    Notification::make()
                        ->title('Vous avez déjà une perm du soir')
                        ->color('danger')
                        ->send();
                } elseif ($existingAstreinte) {
                    Notification::make()
                        ->title('Il n\'y a plus de places pour cette astreinte')
                        ->color('danger')
                        ->send();
                }
            }
        }
    }

    /**
     * Determine the color for "shotgun 1" action.
     *
     * @param  mixed $record
     * @return string|null
     */
    private static function determineColor1($record)
    {
        if ($record->creneau == "M") {
            $astreinteType = "Matin 1";
        } elseif ($record->creneau == "D") {
            $astreinteType = "Déjeuner 1";
        } elseif ($record->creneau == "S") {
            $astreinteType = "Soir 1";
        }
        if (
            Astreinte::where('creneau_id', $record->id)->where('member_id', Filament::auth()->id())
            ->where('astreinte_type', $astreinteType)->first()
        ) {
            return 'success';
        }
        if (
            Astreinte::where('creneau_id', $record->id)->where('astreinte_type', $astreinteType)->first()
        ) {
            return 'danger';
        }
    }

    /**
     * Determine the color for "shotgun 2" action.
     *
     * @param  mixed $record
     * @return string|null
     */
    private static function determineColor2($record)
    {
        if ($record->creneau == "M") {
            $astreinteType = "Matin 2";
        } elseif ($record->creneau == "D") {
            $astreinteType = "Déjeuner 2";
        } elseif ($record->creneau == "S") {
            $astreinteType = "Soir 2";
        }
        if (
            Astreinte::where('creneau_id', $record->id)->where('member_id', Filament::auth()->id())->where('astreinte_type', $astreinteType)->first()
        ) {
            return 'success';
        }
        if (
            ($astreinteType == "Soir 2" && Astreinte::where('creneau_id', $record->id)->count() >= 3) || ($astreinteType != "Soir 2" && Astreinte::where('creneau_id', $record->id)            ->where('astreinte_type', $astreinteType)->first())
        ) {
            return 'danger';
        }
    }

    function getDateSamediAvant(): Carbon
    {
        $aujourdHui = Carbon::now();
        return $aujourdHui->copy()->previous(Carbon::SATURDAY);
    }

    function getDateSamediApres(): Carbon
    {
        $aujourdHui = Carbon::now();
        return $aujourdHui->copy()->next(Carbon::SATURDAY);
    }
}
