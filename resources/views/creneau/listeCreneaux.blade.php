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
    <h2>Créneaux pour les journées sélectionnées :</h2>



    @foreach($creneaux->groupBy(function($date) {
    return Carbon\Carbon::parse($date->date)->format('W');
}) as $week => $creneauxWeek)
        <p>Semaine {{ $week }}</p>
        @foreach($creneauxWeek->groupBy('date') as $date => $creneauxDate)
               Date: {{ $date }}
               <br>
            @foreach($creneauxDate as $creneau)
                {{$creneau->creneau}}
                <form action="{{ route('creneau.associate-perm',  $creneau)}}" method="post">
                    @csrf
                    <select name="perm_id">
                        @foreach($perms as $perm)
                            <option value="{{ $perm->id }}" {{ $creneau->perm_id == $perm->id ? 'selected' : '' }}>
                                {{ $perm->nom }} - {{ $perm->theme }}
                            </option>
                        @endforeach
                    </select>
                    <button type="submit">Associer</button>
                </form>
            @endforeach
        @endforeach
    @endforeach


@else
    <p>Aucun créneau trouvé pour la journée sélectionnée.</p>
@endif

</body>
</html>
