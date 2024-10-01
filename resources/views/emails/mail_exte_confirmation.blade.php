<!DOCTYPE html>
<html>
<head>
    <title>Confirmation de réservation</title>
</head>
<body>
<p>Hello {{ $record->etu_nom_prenom }} !</p>
<p>Nous t'envoyons ce mail pour te dire que ta demande d'exté pour <strong>{{ $record->exte_nom_prenom }}</strong> du <strong> {{ $record->exte_date_debut }} au {{ $record->exte_date_fin }}</strong></p>
<p>Pour que ton exté rentre, il faudra qu'iel rentre <b>en ta compagnie</b> absolument avec une <b>pièce d'identité</b> ainsi que <b>ce mail</b> attestant que la venue de ton exté est autorisée./p>

<p>Bonne journée</p>
<strong>

    <p>Discart Pol & Wallon Thomas <br>
        07.67.24.05.07 & 06.33.77.01.53 <br>
        Présidence du Pic'Asso A24 - Foyer étudiant UTC</p>
</strong>
</body>
</html>

