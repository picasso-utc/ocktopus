<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Ajouter une TV</title>
</head>
<body>
<form action="{{ route('TV.store') }}" method="post" enctype="multipart/form-data">
    @csrf
    <label for="name">Nom :</label>
    <input type="text" name="name" id="name">

    <select name="selected_link" id="selected_link">
        @foreach($links as $link)
            <option value="{{ $link->id }}">
                {{ $link->name }} - {{ $link->url }}
            </option>
        @endforeach
    </select>
    <button type="submit">Créer</button>
</form>
</body>
</html>
