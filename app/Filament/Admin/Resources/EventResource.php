<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\EventResource\Pages;
use Illuminate\Support\Facades\Mail;
use App\Mail\EventMail;
use Filament\Notifications\Notification;
use App\Models\Events;
use App\Models\Shotgun;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;

class EventResource extends Resource
{
    protected static ?string $model = Events::class;
    protected static ?string $navigationIcon = 'heroicon-o-megaphone';
    protected static ?string $navigationGroup = 'Général';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('titre')->required(),
                DateTimePicker::make('ouverture')->required(),
                DateTimePicker::make('debut_event')->nullable(),
                DateTimePicker::make('fin_event')->nullable(),
                TextInput::make('nombre_places')->numeric()->required(),
            ]);
    }
    
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('titre')->sortable()->searchable(),
                TextColumn::make('ouverture')->dateTime(),
                TextColumn::make('debut_event')->dateTime(),
                TextColumn::make('fin_event')->dateTime(),
                TextColumn::make('nombre_places'),
                TextColumn::make('shotguns_count')
                    ->label('Places restantes')
                    ->getStateUsing(fn ($record) => $record->nombre_places - $record->shotguns()->count()),
            ])
            ->filters([
                Filter::make('A venir')
                    ->query(fn ($query) => $query->where('debut_event', '>=', now()))
                    ->default()
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Action::make('voir_inscrits')
                    ->label('Voir les inscrit.e.s')
                    ->icon('heroicon-o-eye')
                    ->modalIcon('heroicon-o-eye')
                    ->modalAlignment('center')
                    ->modalHeading('Liste des inscrit.e.s')
                    ->modalDescription('Voici la liste des participants inscrit.e.s à cet événement.')
                    ->modalContent(fn ($record) => view('filament.admin.modals.inscrits-event', ['event' => $record]))
                    ->modalFooterActions([
                        Action::make('envoyer_mail_tous')
                            ->label('Envoyer mail à tous.tes')
                            ->icon('heroicon-o-envelope')
                            ->action(fn ($record) => static::envoyerMailTous($record))
                            ->requiresConfirmation()
                            ->successNotificationTitle('Emails envoyés !'),
                        Action::make('ajouter_inscrit')
                            ->label('Ajouter quelqu\'un')
                            ->icon('heroicon-o-user-plus')
                            ->form([
                                TextInput::make('email')
                                    ->label('Email du participant')
                                    ->email()
                                    ->required(),
                            ])
                            ->action(fn ($data, $record) => static::ajouterInscrit($record, $data['email']))
                            ->successNotificationTitle('Participant ajouté !'),
                    ])
                    ->modalFooterActionsAlignment('center')
                    ->modalSubmitAction(false)
                    ->modalCancelAction(false),
            ]);
    }
    
    public static function envoyerMailTous($event)
    {
        $emails = $event->shotguns->pluck('email')->toArray();

        if (empty($emails)) {
            Notification::make()
                ->title("Aucun inscrit")
                ->body("Il n'y a aucun inscrit à cet événement.")
                ->danger()
                ->send();
            return;
        }

        foreach ($emails as $email) {
            Mail::to($email)->send(new EventMail($event));
        }

        Notification::make()
            ->title("Emails envoyés")
            ->body("Tous les participants ont reçu un email !")
            ->success()
            ->send();
    }

    public static function envoyerMailPerso($record, $email)
    {
        Mail::to($email)->send(new EventMail($record));

        Notification::make()
            ->title("Email envoyé")
            ->body("Un email a été envoyé à $email.")
            ->success()
            ->send();
    }

    public static function supprimerInscrit($record, $email)
    {
        $inscrit = $record->shotguns()->where('email', $email)->first();
        $inscrit->delete();

        Notification::make()
            ->title("Inscription supprimée")
            ->body("$email a été retiré de l'événement.")
            ->success()
            ->send();
    }

    public static function ajouterInscrit($event, $email)
    {
        if (Shotgun::where('email', $email)->where('events_id', $event->id)->exists()) {
            Notification::make()
                ->title("Déjà inscrit")
                ->body("Cet email est déjà inscrit à cet événement.")
                ->warning()
                ->send();
            return;
        }

        if ($event->shotguns()->count() >= $event->nombre_places) {
            Notification::make()
                ->title("Événement complet")
                ->body("Désolé, il n'y a plus de places disponibles...")
                ->danger()
                ->send();
            return;
        }

        $event->shotguns()->create(['email' => $email]);

        Notification::make()
            ->title("Inscription réussie")
            ->body("$email a été ajouté à l'événement.")
            ->success()
            ->send();
    } 

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEvents::route('/'),
            'create' => Pages\CreateEvent::route('/create'),
            'edit' => Pages\EditEvent::route('/{record}/edit'),
        ];
    }
}
