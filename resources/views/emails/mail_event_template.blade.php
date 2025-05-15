@php
    use \Carbon\Carbon;
@endphp
<!DOCTYPE html>
<html>

    <head>
        <title>{{ $event->titre }}</title>
    </head>

    <body>
        <p>Helloooooooo,
        <p>Si tu reçois ce mail, c’est que tu as réussi le shotgun pour la {{ $event->titre }}</p>
        <p>Comme indiqué sur les posts, RDV au pic le {{ Carbon::parse($event->debut_event)->locale('fr')->translatedFormat('l j F Y à H:i') }} pour la dégust’.</p>
        <p>La dégustation durera environ {{ Carbon::parse($event->debut_event)->diffInMinutes(Carbon::parse($event->fin_event)) / 60 }}h et coûtera 1€ symbolique (à avoir sur Pay’UT du coup).</p>
        <p>Si jamais tu as une galère au dernier moment, penses à nous prévenir, ou alors proposer à un.e de tes potes, ça serait dommage de perdre la place !</p>
        <p>C’est tout good pour les infos, on se retrouve {{ Carbon::parse($event->debut_event)->locale('fr')->translatedFormat('l') }} :))</p>
        <p>Le PIC’Asso qui vous aime ❤️</p>
    </body>
</html>