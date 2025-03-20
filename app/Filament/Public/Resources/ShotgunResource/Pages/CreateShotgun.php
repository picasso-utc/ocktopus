<?php

namespace App\Filament\Public\Resources\ShotgunResource\Pages;

use App\Filament\Public\Resources\ShotgunResource;
use App\Models\Events;
use App\Models\Shotgun;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateShotgun extends CreateRecord
{
    protected static string $resource = ShotgunResource::class;
    protected static ?string $title = 'Shotgun Event';
    
    protected function getCreateFormAction(): \Filament\Actions\Action
    {
        return parent::getCreateFormAction()
            ->label('Shotgun un event');
    }

    protected function beforeCreate(): void
    {
        $alreadyRegistered = Shotgun::where('events_id', $this->data['events_id'])
        ->where('email', $this->data['email'])
        ->exists();

        if ($alreadyRegistered) {
            Notification::make()
                ->title("Déjà inscrit")
                ->body("Vous êtes déjà inscrit.e à cet événement.")
                ->danger()
                ->send();

            $this->halt();
        }

        $event = Events::findOrFail($this->data['events_id']);
        if ($event->shotguns()->count() >= $event->nombre_places) {
            Notification::make()
                ->title("Événement complet")
                ->body("Désolé, il n'y a plus de places disponibles pour cet événement.")
                ->danger()
                ->send();
    
            $this->halt();
        }
    }
}
