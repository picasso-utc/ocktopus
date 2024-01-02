<?php

namespace App\Filament\Resources\AstreinteResource\Pages;

use App\Filament\Resources\AstreinteResource;
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

class EditAstreinte extends EditRecord
{
    protected static string $resource = AstreinteResource::class;

    protected static ?string $title = "Notation des perms";
    protected function formMatin(Form $form): Form
    {
        return $form
        ->schema([
            Radio::make('note_orga')
                ->label('Note Organisation')
                ->options([
                    '3' => 'Nickel, avec un ménage de qualité',
                    '2' => 'Rien à redire',
                    '1' => 'Améliorable mais ça va',
                    '0' => 'Bof, iels auraient pu faire des efforts',
                ])
                ->required(),

            Textarea::make('commentaire')
                ->label('Commentaire')
            // Add other fields specific to Type1
        ]);
    }

    protected function formMidi(Form $form): Form
    {
        return $form
            ->schema([
                Radio::make('note_menu')
                    ->label('Note Menu')
                    ->options([
                        '3' => 'Gastro',
                        '2' => 'Pas mal',
                        '1' => 'Satisfaisant',
                        '0' => 'Horrible',
                    ]),
                Radio::make('note_orga')
                    ->label('Note Organisation')
                    ->options([
                        '3' => 'Nickel, avec un ménage de qualité',
                        '2' => 'Rien à redire',
                        '1' => 'Améliorable mais ça va',
                        '0' => 'Bof, iels auraient pu faire des efforts',
                    ])
                    ->required(),
                Textarea::make('commentaire')
                    ->label('Commentaire')
                // Add other fields specific to Type1
            ]);
    }
    protected function formSoir(Form $form): Form
    {
        return $form
            ->schema([
                Radio::make('note_menu')
                    ->label('Note Menu')
                    ->options([
                        '3' => 'Gastro',
                        '2' => 'Pas mal',
                        '1' => 'Satisfaisant',
                        '0' => 'Horrible',
                    ]),
                Radio::make('note_deco')
                    ->label('Note Décoration')
                    ->options([
                        '3' => 'Le pic s\'est refait une beauté',
                        '2' => 'Y\'a eu du travail',
                        '1' => 'Quelques éléments par ci par là',
                        '0' => 'On la cherche toujours',
                    ])
                    ->required(),
                Radio::make('note_anim')
                    ->label('Note Animation et ambiance')
                    ->options([
                        '3' => 'Dancing de folie et anims de qualité',
                        '2' => 'Des efforts avec des anims',
                        '1' => 'Un soir lambda',
                        '0' => 'Bof, iels auraient pu faire des efforts',
                    ])
                    ->required(),
                Radio::make('note_orga')
                    ->label('Note Organisation')
                    ->options([
                        '3' => 'Nckel, avec un ménage de qualité',
                        '2' => 'Rien à redire',
                        '1' => 'Améliorable mais ça va',
                        '0' => 'Bof, iels auraient pu faire des efforts',
                    ])
                    ->required(),
                Textarea::make('commentaire')
                    ->label('Commentaire')
                // Add other fields specific to Type1
            ]);
    }
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
            ->schema([
            ]);
    }
    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
