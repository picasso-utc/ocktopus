<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Éditer des TVs</title>
</head>

<body>

    <form action="{{route('TV.update', $tv)}}"  method ="POST">
        @csrf
        @method('PUT')
        <label for="selected_link">Choisir un lien :</label>
        <select name="selected_link" id="selected_link">
            @foreach($links as $link)
                <option value="{{ $link->id }}" {{ $link->id === $tv->link_id ? 'selected' : '' }}>
                    {{ $link->name }} - {{ $link->url }}
                </option>
            @endforeach
        </select>
        <button type="submit">Editer</button>

    </form>
</body>
</html>
