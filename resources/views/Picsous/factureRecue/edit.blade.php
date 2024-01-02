<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Mettre à jour une facture recue</title>
</head>
<body>
<form action="{{route('Picsous.facturerecue.update', $factureRecue)}}" method="post" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <label for="nom_entreprise">Nom :</label>
    <input type="text" name="nom_entreprise" id="nom_entreprise" value={{$factureRecue->nom_entreprise}}>

    <label for="prix">Prix :</label>
    <input type="text" name="prix" id="prix" value={{$factureRecue->prix}}>

    <label for="state">État :</label>
    <select name="state" id="state">
        <option value=D>Facture à payer</option>
        <option value=R>Facture à rembourser</option>
        <option value=E>Facture en attente</option>
        <option value=P>Facture payée</option>
    </select>

    <label for="tva">TVA :</label>
    <input type="text" name="tva" id="tva" value={{$factureRecue->tva}}>

    <label for="categorie_id">Categories(<a href="{{route('Picsous.categorie.create')}}">+</a>)</label>
    <select name="categorie_id" id="categorie_id">
        @foreach($categorieFactureRecues as $categorieFactureRecue)
            <option value="{{$categorieFactureRecue->id}}">
                {{$categorieFactureRecue->nom}}
            </option>
        @endforeach
    </select>

    <label for="categorie_prix">Categorie prix :</label>
    <input type="text" name="categorie_prix" id="categorie_prix">

    <button type="submit">Modifier</button>
</form>
</body>
</html>
