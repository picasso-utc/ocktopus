<?php

namespace App\Filament\Public\Resources;

use Carbon\Carbon;
use App\Filament\Public\Resources\ShotgunResource\Pages;
use App\Models\Events;
use App\Models\Shotgun;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;

class ShotgunResource extends Resource
{
    protected static ?string $model = Shotgun::class;
    protected static ?string $navigationIcon = 'heroicon-o-rocket-launch';

    public static function form(Form $form): Form
    {
        $userMail = session('user')->email;

        return $form
            ->schema([
                TextInput::make('email')->email()->default($userMail)->required(),
                Select::make('events_id')
                    ->label('Événement')
                    ->options(function () {
                        $userEmail = session('user')->email;

                        return Events::where('ouverture', '<=', now())
                            ->where('debut_event', '>=', now())
                            ->whereRaw('(SELECT COUNT(*) FROM shotgun WHERE shotgun.events_id = events.id) < events.nombre_places')
                            ->whereNotExists(function ($query) use ($userEmail) {
                                $query->selectRaw(1)
                                    ->from('shotgun')
                                    ->whereColumn('shotgun.events_id', 'events.id')
                                    ->where('shotgun.email', $userEmail);
                            })
                            ->orderBy('debut_event')
                            ->get()
                            ->mapWithKeys(function ($event) {
                                $debut = Carbon::parse($event->debut_event);
                                $fin = Carbon::parse($event->fin_event);

                                $label = sprintf(
                                    '%s : %s %d %s %s-%s',
                                    $event->titre,
                                    ucfirst($debut->translatedFormat('l')),
                                    $debut->day,
                                    $debut->translatedFormat('F'),
                                    $debut->format('H:i'),
                                    $fin->format('H:i')
                                );

                                return [$event->id => $label];
                            });
                    })
                    ->required()        
            ]);
    }
    
    public static function table(Table $table): Table
    {
        $user = session('user');
        $userMail = $user->email;

        return $table
            ->modifyQueryUsing(fn ($query) => $query->where('email', $userMail))
            ->columns([
                TextColumn::make('email')->sortable()->searchable(),
                TextColumn::make('event.titre')->label('Événement')->sortable(),
                TextColumn::make('event.debut_event')
                    ->label('Début de l\'évent')    
                    ->formatStateUsing(fn ($state) => ucfirst(Carbon::parse($state)->locale('fr')->translatedFormat('l j F Y à H:i')))
            ])
            ->filters([
                Filter::make('A venir')
                ->query(fn ($query) => 
                    $query->whereHas('event', function ($q) {
                        $q->where('debut_event', '>=', now());
                    })
                )
                ->default(),            
            ])
            ->actions(
                [
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ]
            );
    }    

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label("S'inscrire"),
        ];
    } 

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListShotguns::route('/'),
            'create' => Pages\CreateShotgun::route('/create'),
            'edit' => Pages\EditShotgun::route('/{record}/edit'),
        ];
    }
}
