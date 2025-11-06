<?php

namespace App\Filament\Admin\Resources\EventResource\Components;

use Livewire\Component;
use App\Models\Events;
use Illuminate\Support\Facades\Mail;
use App\Mail\EventMail;
use Filament\Notifications\Notification;

class ListInscritsModal extends Component
{
    public Events $event;
    
    public function envoyerMailPerso($email)
    {
        Mail::to($email)->send(new EventMail($this->event));

        Notification::make()
            ->title("Email envoyé")
            ->body("Un email a été envoyé à $email.")
            ->success()
            ->send();
    }

    public function supprimerInscrit($email)
    {
        $inscrit = $this->event->shotguns()->where('email', $email)->first();
        
        if ($inscrit) {
            $inscrit->delete();

            Notification::make()
                ->title("Inscription supprimée")
                ->body("$email a été retiré de l'événement.")
                ->success()
                ->send();
        } else {
            Notification::make()
                ->title("Erreur")
                ->body("Impossible de trouver cet inscrit.")
                ->danger()
                ->send();
        }
    }

    public function render()
    {
        return view('filament.admin.resources.event-resource.components.list-inscrits-modal');
    }
}