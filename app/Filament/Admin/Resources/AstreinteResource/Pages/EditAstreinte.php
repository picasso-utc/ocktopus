<?php

namespace App\Filament\Admin\Resources\AstreinteResource\Pages;

use App\Filament\Admin\Resources\AstreinteResource;
use Filament\Actions;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;
use Filament\Tables\Columns\CheckboxColumn;
use Filament\Tables\Columns\SelectColumn;
use Illuminate\Support\Number;

/**
 * Edit record page for AstreinteResource.
 */
class EditAstreinte extends EditRecord
{
    /**
     * The associated resource class for this page.
     *
     * @var string
     */
    protected static string $resource = AstreinteResource::class;

    /**
     * The title for the edit record page.
     *
     * @var string|null
     */
    protected static ?string $title = "Notation des perms";

    /**
     * Define the form structure for the "Matin" type.
     *
     * @param  Form $form
     * @return Form
     */
    protected function formMatin(Form $form): Form
    {
        return $form
            ->schema(
                [
                Radio::make('note_orga')
                    ->label('Note Organisation')
                    ->options(
                        [
                        '4' => 'Nickel, avec un ménage de qualité',
                        '3' => 'Rien à redire',
                        '2' => 'Améliorable mais ça va',
                        '1' => 'Bof, iels auraient pu faire des efforts',
                        ]
                    )
                    ->required(),

                Textarea::make('commentaire')
                    ->label('Commentaire')
                ]
            );
    }
    /**
     * Define the form structure for the "Midi" type.
     *
     * @param  Form $form
     * @return Form
     */
    protected function formMidi(Form $form): Form
    {
        return $form
            ->schema(
                [
                Radio::make('note_menu')
                    ->label('Note Menu')
                    ->options(
                        [
                        '4' => 'Gastro',
                        '3' => 'Pas mal',
                        '2' => 'Satisfaisant',
                        '1' => 'Horrible',
                        ]
                    ),
                Radio::make('note_orga')
                    ->label('Note Organisation')
                    ->options(
                        [
                        '4' => 'Nickel, avec un ménage de qualité',
                        '3' => 'Rien à redire',
                        '2' => 'Améliorable mais ça va',
                        '1' => 'Bof, iels auraient pu faire des efforts',
                        ]
                    )
                    ->required(),
                Textarea::make('commentaire')
                    ->label('Commentaire')
                ]
            );
    }

    /**
     * Define the form structure for the "Soir" type.
     *
     * @param  Form $form
     * @return Form
     */
    protected function formSoir(Form $form): Form
    {
        return $form
            ->schema(
                [
                Radio::make('note_menu')
                    ->label('Note Menu')
                    ->options(
                        [
                        '4' => 'Gastro',
                        '3' => 'Pas mal',
                        '2' => 'Satisfaisant',
                        '1' => 'Horrible',
                        ]
                    ),
                Radio::make('note_deco')
                    ->label('Note Décoration')
                    ->options(
                        [
                        '3' => 'Le pic s\'est refait une beauté',
                        '2' => 'Y\'a eu du travail',
                        '1' => 'Quelques éléments par ci par là',
                        '0' => 'On la cherche toujours',
                        ]
                    )
                    ->required(),
                Radio::make('note_anim')
                    ->label('Note Animation et ambiance')
                    ->options(
                        [
                        '4' => 'Dancing de folie et anims de qualité',
                        '3' => 'Des efforts avec des anims',
                        '2' => 'Un soir lambda',
                        '1' => 'Bof, iels auraient pu faire des efforts',
                        ]
                    )
                    ->required(),
                Radio::make('note_orga')
                    ->label('Note Organisation')
                    ->options(
                        [
                        '4' => 'Nickel, avec un ménage de qualité',
                        '3' => 'Rien à redire',
                        '2' => 'Améliorable mais ça va',
                        '1' => 'Bof, iels auraient pu faire des efforts',
                        ]
                    )
                    ->required(),
                Textarea::make('commentaire')
                    ->label('Commentaire')
                ]
            );
    }

    /**
     * Define the form structure based on the Astreinte type.
     *
     * @param  Form $form
     * @return Form
     */
    public function form(Form $form): Form
    {
        $astreinteType = $this->record->astreinte_type;
        if ($astreinteType === 'Matin 1' || $astreinteType === 'Matin 2') {
            return $this->formMatin($form);
        } elseif ($astreinteType === 'Déjeuner 1' || $astreinteType === 'Déjeuner 2') {
            return $this->formMidi($form);
        }
        elseif ($astreinteType === 'Soir 1' || $astreinteType === 'Soir 2' || $astreinteType === 'Soir 3' || $astreinteType === 'Soir 4') {
            return $this->formSoir($form);
        }

        return $form
            ->schema(
                [
                ]
            );
    }
    protected function getHeaderActions(): array
    {
        return [
        ];
    }
}
