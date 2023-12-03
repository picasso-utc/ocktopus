<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Les médias</title>
</head>
<body>
    <p>
        <a href="{{route('media.create')}}">Créer un media</a>
    </p>
    <br>
@foreach($medias as $media)
    <p>
        {{$media->name}}
    </p>
    <p>
        <a href="{{route('media.edit', $media) }}">Editer</a>
    </p>
    <form action="{{route('media.destroy', $media)}}"  method ="POST">
        @csrf
        @method('DELETE')
        <button type="submit"> Supprimer </button>
    </form>


    <br>

@endforeach

</body>
</html>
