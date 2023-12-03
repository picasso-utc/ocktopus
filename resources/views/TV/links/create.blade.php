<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Créer un lien</title>
</head>
<body>
    <form action="{{ route('link.store') }}" method="post" enctype="multipart/form-data">
    @csrf
        <label for="name">Nom :</label>
        <input type="text" name="name" id="name">

        <label for="url">Url :</label>
        <input type="text" name="url" id="url">

        <button type="submit">Créer</button>
    </form>
</body>
</html>
