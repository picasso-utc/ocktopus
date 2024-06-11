<?php

namespace App\Filament\Treso\Resources\FactureRecueResource\Pages;

use App\Filament\Treso\Resources\FactureRecueResource;
use App\Models\CategorieFacture;
use App\Models\FactureRecue;
use App\Models\MontantCategorie;
use App\Models\Semestre;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateFactureRecue extends CreateRecord
{
    protected static string $resource = FactureRecueResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function afterCreate(): void
    {
        $semestre = Semestre::find($this->record->semestre_id);
        $annee = date("Y", strtotime($semestre->startOfSemestre));
        $category_code = CategorieFacture::find($this->record->categoriePrix->first()->categorie_id)->code;
        $facture_number = $semestre->state[0] . $annee . "-" . $category_code . $this->record->id;
        FactureRecue::find($this->record->id)->update(['facture_number'=>$facture_number]);
    }
}
