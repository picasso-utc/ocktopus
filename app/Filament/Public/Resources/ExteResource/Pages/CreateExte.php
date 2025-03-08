<?php

namespace App\Filament\Public\Resources\ExteResource\Pages;

use App\Models\Exte;
use App\Filament\Public\Resources\ExteResource;
use Illuminate\Validation\ValidationException;
use Filament\Resources\Pages\CreateRecord;

class CreateExte extends CreateRecord
{
    protected static ?string $title = 'Demande d\'exté';
    protected static bool $canCreateAnother = false;
    protected static string $resource = ExteResource::class;

    protected function getCreateFormAction(): \Filament\Actions\Action
    {
        return parent::getCreateFormAction()
            ->label('Demander');
    }

    protected function beforeCreate(): void
    {
        $userEmail = session('user')->email;
        $updates = request()['components'][0]['updates'];

        $startDate = $updates['data.exte_date_debut'];
        $endDate = $updates['data.exte_date_fin'];        

        $overlappingRequest = Exte::where('etu_mail', $userEmail)
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('exte_date_debut', [$startDate, $endDate])
                    ->orWhereBetween('exte_date_fin', [$startDate, $endDate])
                    ->orWhere(function ($q) use ($startDate, $endDate) {
                        $q->where('exte_date_debut', '<=', $startDate)
                            ->where('exte_date_fin', '>=', $endDate);
                    });
            })
            ->exists();

            if ($overlappingRequest) {
                throw ValidationException::withMessages([
                    'exte_date_debut' => "Vous avez déjà une demande sur cette période.",
                    'exte_date_fin' => "Vous avez déjà une demande sur cette période.",
                ]);
            }            
    }
}
