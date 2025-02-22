<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau des Demandes</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #4ade80;
        }
    </style>
</head>
<body>
<h1>Tableau des Extés</h1>
<table>
    <thead>
    <tr>
        <th>Date début</th>
        <th>Date fin</th>
        <th>Garant UTC</th>
        <th>Exte</th>
        <th>Cas Garant</th>
        <!-- Ajoute d'autres colonnes si nécessaire -->
    </tr>
    </thead>
    <tbody>
    @foreach ($demandes as $demande)
        <tr>
            <td>{{ $demande->exte_date_debut }}</td>
            <td>{{ $demande->exte_date_fin }}</td>
            <td>{{ $demande->etu_nom_prenom }}</td>
            <td>{{ $demande->exte_nom_prenom }}</td>
            <td>{{ $demande->etu_cas }}</td>
            <!-- Ajoute d'autres champs si nécessaire -->
        </tr>
    @endforeach
    </tbody>
</table>
</body>
</html>
