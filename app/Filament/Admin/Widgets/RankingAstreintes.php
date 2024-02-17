<?php

namespace App\Filament\Admin\Widgets;

use App\Filament\Admin\Resources\AstreinteResource;
use App\Filament\Admin\Resources\PermResource;
use App\Models\Astreinte;
use App\Models\Creneau;
use App\Models\Perm;
use App\Models\Semestre;
use BladeUI\Icons\Components\Icon;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

/**
 * Widget for displaying a ranking of perms with various notes.
 *
 * This widget presents a table of prms, including columns for name, overall note, and specific notes
 * for organization, decoration, animation, and menu. The notes are displayed using different icons and colors
 * based on their averages.
 *
 * @package App\Filament\Admin\Widgets
 */
class RankingAstreintes extends BaseWidget
{

    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 'full';




    public function table(Table $table): Table
    {
        $semestreActif = Semestre::where('activated', true)->first();
        return $table
            ->query(PermResource::getEloquentQuery()->where('validated',true)->where('semestre',$semestreActif->id)
            )
            ->columns([
                Tables\Columns\TextColumn::make('nom')
                    ->label('Nom')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('id')
                    ->label('Note')
                    ->color(fn($record) => $this->handlColorNoted($record))
                    ->formatStateUsing(fn ($record) => $this->nbNotation($record)),
                Tables\Columns\IconColumn::make('creneaux')
                    ->label('Note orga')
                    ->Icon(fn ($record) => $this->noteIcon($record, 'note_orga'))
                    ->color(fn ($record) => $this->noteColor($record, 'note_orga')),
                Tables\Columns\IconColumn::make('description')
                    ->label('Note déco')
                    ->Icon(fn ($record) => $this->noteIcon($record, 'note_deco'))
                    ->color(fn ($record) => $this->noteColor($record, 'note_deco')),
                Tables\Columns\IconColumn::make('periode')
                    ->label('Note anim')
                    ->Icon(fn ($record) => $this->noteIcon($record, 'note_anim'))
                    ->color(fn ($record) => $this->noteColor($record, 'note_anim')),
                Tables\Columns\IconColumn::make('membres')
                    ->label('Note menu')
                    ->Icon(fn ($record) => $this->noteIcon($record, 'note_menu'))
                    ->color(fn ($record) => $this->noteColor($record, 'note_menu')),
            ]);
    }



    /**
     * Calculate and return the number of notations with a non-null 'note_orga' and the total number of notations for a given record.
     *
     * @param mixed $record The record for which to calculate notations.
     *
     * @return string The formatted string representing the number of notations with 'note_orga' on the total number of notations.
     */
    private function nbNotation($record): string
    {
        // Retrieve the IDs of creneaux associated with the given record's perm_id
        $creneauIds = Creneau::query()->where('perm_id', $record->id)->pluck('id')->toArray();

        // Count the total number of Astreintes associated with the creneau IDs
        $nbAstreintes = Astreinte::whereIn('creneau_id', $creneauIds)->count();

        // Count the number of Astreintes with a non-null 'note_orga' associated with the creneau IDs
        $nbAstreintesNotees = Astreinte::whereIn('creneau_id', $creneauIds)->whereNotNull('note_orga')->count();

        // Return the formatted string representing the number of notations with 'note_orga' on the total number of notations
        return $nbAstreintesNotees . '/' . $nbAstreintes;
    }

    /**
     * Determine the color code based on the ratio of notations with 'note_orga' to the total number of notations for a given record.
     *
     * @param mixed $record The record for which to determine the color.
     *
     * @return string The color code ('success', 'warning', 'danger', or 'gray') based on the ratio.
     */
    private function handlColorNoted($record)
    {
        // Retrieve the creneaux associated with the given record's perm_id
        $creneaux = Creneau::query()->where('perm_id', $record->id)->get();

        $nbAstreintes = 0;

        // Count the total number of Astreintes associated with the creneaux
        foreach ($creneaux as $creneau) {
            $nbAstreintes += Astreinte::query()->where('creneau_id', $creneau->id)->count();
        }

        $nbAstreintesNotees = 0;

        // Count the number of Astreintes with a non-null 'note_orga' associated with the creneaux
        foreach ($creneaux as $creneau) {
            $nbAstreintesNotees += Astreinte::query()->where('creneau_id', $creneau->id)->whereNotNull('note_orga')->count();
        }

        // Determine the color code based on the ratio of notations with 'note_orga' to the total number of notations
        if ($nbAstreintes > 0) {
            if (($nbAstreintesNotees / $nbAstreintes) > 0.90) {
                return 'success';
            }

            if (($nbAstreintesNotees / $nbAstreintes) < 0.50) {
                return 'danger';
            } else {
                return 'warning';
            }
        }

        // Return 'gray' if there are no notations
        return 'gray';
    }


    /**
     * Calculate the average note and determine the icon
     *
     * @param mixed $record The record for which to calculate the note and determine the icon.
     * @param string $type The type of note to calculate and determine the icon ('note_orga', 'note_deco', etc.).
     *
     * @return string The icon to be displayed for the calculated note.
     */
    private function noteIcon($record, string $type): string
    {
        $creneauIds = Creneau::query()->where('perm_id', $record->id)->pluck('id')->toArray();

        $nbAstreintesNotees = Astreinte::whereIn('creneau_id', $creneauIds)->whereNotNull($type)->count();

        if ($nbAstreintesNotees > 0) {
            $totalNote = Astreinte::whereIn('creneau_id', $creneauIds)->whereNotNull($type)->sum($type);
            $averageNote = $totalNote / $nbAstreintesNotees;
        }
        else return '';

        if ($averageNote >= 3.5) return 'heroicon-o-star';
        else if ($averageNote > 2.5) return 'heroicon-o-face-smile';
        else if ($averageNote > 1) return 'heroicon-o-face-frown';
        else return 'heroicon-o-trash';
    }

    /**
     * Determine the color to be applied based on the average note for the specified type.
     *
     * @param mixed $record The record for which to determine the color.
     * @param string $type The type of note for which to determine the color ('note_orga', 'note_deco', etc.).
     *
     * @return string The color to be applied based on the average note.
     */
    private function noteColor($record, string $type): string
    {
        $creneauIds = Creneau::query()->where('perm_id', $record->id)->pluck('id')->toArray();

        $nbAstreintesNotees = Astreinte::whereIn('creneau_id', $creneauIds)->whereNotNull($type)->count();

        $averageNote = 0;

        if ($nbAstreintesNotees > 0) {
            $totalNote = Astreinte::whereIn('creneau_id', $creneauIds)->whereNotNull($type)->sum($type);
            $averageNote = $totalNote / $nbAstreintesNotees;
        }


        if ($averageNote >= 3.5) return 'warning';
        else if ($averageNote > 2.5) return 'success';
        else if ($averageNote > 1) return 'warning';
        else return 'danger';
    }

}
