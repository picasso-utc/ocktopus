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
<p>Nous t'envoyons ce mail pour te dire que ta demande d'exté pour <strong>{{ $record->exte_nom_prenom }}</strong> du <strong> {{Carbon::parse($record->exte_date_debut)->translatedFormat('d F Y') }} au {{Carbon::parse($record->exte_date_fin)->translatedFormat('d F Y')  }}</strong> a bien été validée</p>
<p>Pour que ton exté rentre, il faudra qu'iel rentre <b>en ta compagnie</b> absolument avec une <b>pièce d'identité</b> ainsi que <b>ce mail</b> attestant que la venue de ton exté est autorisée.</p>

<p>Bonne journée</p>
<strong>

    <p>Saglio Geo & Sinoquet Hugo <br>
        07.69.22.41.99 & 06.18.84.37.66 <br>
        Présidence du Pic'Asso P25 - Foyer étudiant UTC</p>
</strong>
</body>
</html>

