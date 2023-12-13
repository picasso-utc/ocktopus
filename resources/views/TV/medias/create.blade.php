<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Ajouter des médias</title>

</head>
<body>
<form action="{{ route('media.store') }}" method="post" enctype="multipart/form-data">
    @csrf

    <label for="name">Media :</label>
    <input type="text" name="name" id="name">

    <label for="media_type">Type de média :</label>
    <select name="media_type" id="media_type">
        <option value="Image">Image</option>
        <option value="Video">Vidéo</option>
    </select>

    <label for="activate">Activer :</label>
    <input type="checkbox" name="activate" id="activate" value="1">

    <label for="times">Temps (en secondes pour image / en nombre de fois pour video) :</label>
    <input type="number" name="times" id="times" value="1">

    <label for="media_path">Fichier média :</label>
    <input type="file" name="media_path" id="media_path" >

    <button type="submit">Ajouter le média</button>
</form>
</body>
</html>
