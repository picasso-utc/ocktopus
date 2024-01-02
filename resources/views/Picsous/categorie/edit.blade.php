<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Mettre à jour une catégorie</title>
</head>
<body>
<form action="{{route('Picsous.categorie.update', $categorieFacture)}}" method="post" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <label for="nom">Nom :</label>
    <input type="text" name="nom" id="nom" value={{$categorieFacture->nom}}>

    <label for="code">Code :</label>
    <input type="text" name="code" id="code" value={{$categorieFacture->code}}>

    <p>Sous-catégorie associé(s) :</p>


    <button type="submit">Modifier</button>
</form>
</body>
</html>
