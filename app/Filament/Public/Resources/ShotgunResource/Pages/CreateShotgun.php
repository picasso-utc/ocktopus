<?php

namespace App\Filament\Public\Resources\ShotgunResource\Pages;

use App\Filament\Public\Resources\ShotgunResource;
use App\Models\Events;
use App\Models\Shotgun;
use Carbon\Carbon;
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
        $email = $this->data['email'];
        $eventId = $this->data['events_id'];
        $event = Events::findOrFail($eventId);

        $alreadyRegistered = Shotgun::where('events_id', $eventId)
            ->where('email', $email)
            ->exists();

        if ($alreadyRegistered) {
            Notification::make()
                ->title("Déjà inscrit")
                ->body("Vous êtes déjà inscrit.e à cet événement.")
                ->danger()
                ->send();

            $this->halt();
        }

        if ($event->shotguns()->count() >= $event->nombre_places) {
            Notification::make()
                ->title("Événement complet")
                ->body("Désolé, il n'y a plus de places disponibles pour cet événement.")
                ->danger()
                ->send();

            $this->halt();
        }

        $shotguns = Shotgun::where('email', $email)
            ->with('event')
            ->get();

        $currentWeek = Carbon::parse($event->debut_event)->weekOfYear;
        $currentYear = Carbon::parse($event->debut_event)->year;

        $countSameWeek = $shotguns->filter(function($shotgun) use ($currentWeek, $currentYear) {
            $eventStart = Carbon::parse($shotgun->event->debut_event);
            return $eventStart->weekOfYear === $currentWeek && $eventStart->year === $currentYear;
        })->count();

        if ($countSameWeek >= 2) {
            Notification::make()
                ->title("Limite d'inscriptions atteinte")
                ->body("Vous êtes déjà inscrit.e à 2 événements cette semaine.")
                ->danger()
                ->send();

            $this->halt();
        }
    }
}
