<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Les perms</title>
</head>
<body>
{{--<p>
    <a href="{{route('perm.create')}}">ajouter une perm</a>
</p>--}}
<div class="container">
    <h1>Liste des Perms</h1>

    @if(count($perms) > 0)
        <table class="table">
            <thead>
            <tr>
                <th>Nom</th>
                <th>Thème</th>
                <th>Description</th>
            </tr>
            </thead>
            <tbody>
            @foreach($perms as $perm)
                <tr>
                    <td>{{ $perm->nom }}</td>
                    <td>{{ $perm->theme }}</td>
                    <td>{{ $perm->description }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @else
        <p>Aucune perm n'a été trouvée.</p>
    @endif
</div>

</body>
</html>
