<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Les liens</title>
</head>
<body>
<p>
</p>
<br>
@foreach($tvs as $tv)
    <p>
        {{$tv->name}} : <a href="{{ route('TV.show', $tv) }}">Visualiser</a>    </p>
    <p>
        <a href="{{route('tv.edit', $tv) }}">Editer</a>

    </p>
    <br>

@endforeach

</body>
</html>
