<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Éditer des médias</title>

    </head>
    <body>
    <form action="{{ route('media.update', $media) }}" method="post" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <label for="name">Media :</label>
        <input type="text" name="name" id="name" value="{{ $media->name }}">

        <label for="activate">Activer :</label>
        <input type="checkbox" name="activate" id="activate" @if($media->activated) checked @endif>

        <label for="times">Temps (en secondes pour image / en nombre de fois pour vidéo) :</label>
        <input type="number" name="times" id="times" value="{{ $media->times }}">


        <label for="media_path">Fichier média :</label>
        <input type="file" name="media_path" id="media_path" ">


        <button type="submit">Éditer le média</button>
    </form>
</body>
</html>
