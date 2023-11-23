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
    <a href="{{route('link.create')}}">Créer un lien</a>
</p>
<br>
@foreach($links as $link)
    <p>
       <a href="{{$link->url}}">{{$link->name}}</a>
    </p>
    <p>
        <a href="{{route('link.edit', $link) }}">Editer</a>
    <form action="{{route('link.destroy', $link)}}"  method ="POST">
        @csrf
        @method('DELETE')
        <button type="submit"> Supprimer </button>
    </form>

    </p>
    <br>

@endforeach

</body>
</html>
