<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Créer une note de frais</title>
</head>
<body>
<form action="{{route('Picsous.notedefrais.store')}}" method="post" enctype="multipart/form-data">
    @csrf
    <label for="nom">Nom :
        <input type="text" name="nom" id="nom"></label>

    <label for="prenom">Prenom :</label>
    <input type="text" name="prenom" id="prenom">

    <br><br>

    <label for="numero_voie">N° voie :</label>
    <input type="text" name="numero_voie" id="numero_voie">

    <label for="rue">Rue :</label>
    <input type="text" name="rue" id="rue">

    <label for="code_postal">Code Postal :</label>
    <input type="text" name="code_postal" id="code_postal">

    <label for="ville">Ville :</label>
    <input type="text" name="ville" id="ville">

    <br><br>

    <label for="email">Email :</label>
    <input type="text" name="email" id="email">

    <label for="state">État :
        <select name="state" id="state">
            <option value="D">Note à payer</option>
            <option value="R">Note à rembourser</option>
            <option value="E">Note en attente</option>
            <option value="P">Note payée</option>
        </select>
    </label>

    <label for="date_facturation">Date :
    <input id="date_facturation" name="date_facturation" type="date"/></label>

    <br><br>

    <label for="description">Description :</label>
    <input type="text" name="description" id="description">

    <label for="prix_unitaire_ttc">Prix unitaire TTC :</label>
    <input type="text" name="prix_unitaire_ttc" id="prix_unitaire_ttc">

    <label for="tva">TVA (%) :</label>
    <input type="text" name="tva" id="tva">

    <label for="quantite">quantite :</label>
    <input type="number" name="quantite" id="quantite">

    <button type="submit">Créer</button>
</form>
</body>
</html>
