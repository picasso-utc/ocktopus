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
                <form action="{{ route('creneau.associate-perm',  $creneau)}}" method="post" class="{{ Carbon\Carbon::parse($creneau->date)->isPast() ? 'disabled-form' : '' }}">
                    @csrf
                    <select name="perm_id" class="perm-select">
                        <option value="" selected></option>
                        @foreach($perms as $perm)
                            <option value="{{ $perm->id }}" {{ $creneau->perm_id == $perm->id ? 'selected' : '' }}>
                                {{ $perm->nom }} - {{ $perm->theme }}
                            </option>
                        @endforeach
                    </select>
                    <button type="submit" class="associate-btn" {{ Carbon\Carbon::parse($creneau->date)->isPast() ? 'disabled' : '' }}>Associer</button>
                </form>
            @endforeach
        @endforeach
    @endforeach

    <script>
        // JavaScript pour désactiver les formulaires associés aux créneaux passés
        document.addEventListener("DOMContentLoaded", function () {
            var disabledForms = document.querySelectorAll('.disabled-form');

            disabledForms.forEach(function (form) {
                var select = form.querySelector('.perm-select');
                var button = form.querySelector('.associate-btn');

                select.disabled = true;
                button.disabled = true;
            });
        });
    </script>

@else
    <p>Aucun créneau trouvé pour la journée sélectionnée.</p>
@endif

</body>
</html>
