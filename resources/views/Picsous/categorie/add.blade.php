<!doctype html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport"
              content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Liste catégorie</title>
    </head>
    <body>
        <form action="{{route('Picsous.categorie.store')}}" method="post" enctype="multipart/form-data">
            @csrf
            <label for="nom">Nom :</label>
            <input type="text" name="nom" id="nom">

            <label for="code">Code :</label>
            <input type="text" name="code" id="code">

            <button type="submit">Créer</button>
        </form>
    </body>
</html>
<?php
