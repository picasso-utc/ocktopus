<!-- resources/views/creneau/listeCreneaux.blade.php -->

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des créneaux</title>
</head>
<body>

<h1>Liste des créneaux</h1>

<form action="{{ route('creneau.listeCreneaux') }}" method="GET">
    @csrf

    <label for="start_date">Date de début :</label>
    <input type="date" id="start_date" name="start_date" required>

    <label for="end_date">Date de fin :</label>
    <input type="date" id="end_date" name="end_date" required>

    <button type="submit">Afficher les créneaux</button>
</form>

@if(isset($creneaux) && count($creneaux) > 0)
    <h2>Créneaux pour la journée sélectionnée :</h2>
    <ul>
        @foreach($creneaux as $creneau)
            <li>{{ $creneau->creneau }} - {{ $creneau->date }}</li>
        @endforeach
    </ul>
@else
    <p>Aucun créneau trouvé pour la journée sélectionnée.</p>
@endif

</body>
</html>
