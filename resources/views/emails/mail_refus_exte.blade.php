@php
    use \Carbon\Carbon;
@endphp
    <!DOCTYPE html>
<html>
<head>
    <title>Confirmation de réservation</title>
</head>
<body>
<p>Hello {{ $record->etu_nom_prenom }} !</p>
<p>Nous t'envoyons ce mail pour te dire que ta demande d'exté pour <strong>{{ $record->exte_nom_prenom }}</strong> du <strong> {{Carbon::parse($record->exte_date_debut)->translatedFormat('d F Y') }} au {{Carbon::parse($record->exte_date_fin)->translatedFormat('d F Y')  }}</strong> a été refusée.</p>
<p>La raison de ce rejet peut venir de l'administration ou bien d'une demande mal remplie (manque du prénom et/ou du nom de votre exté, cas non renseigné, ...). Si vous avez des questions vis-à-vis de ce refus, n'hésitez pas à répondre à ce mail ou à nous envoyer un message sur Instagram !</p>

<p>Bonne journée</p>
<strong>

    <p>Saglio Geo & Sinoquet Hugo <br>
        07.69.22.41.99 & 06.18.84.37.66 <br>
        Présidence du Pic'Asso P25 - Foyer étudiant UTC</p>
</strong>
</body>
</html>

