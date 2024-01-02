<!doctype html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport"
              content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Créer une facture recue</title>
    </head>
    <body>
        <form action="{{route('Picsous.facturerecue.store')}}" method="post" enctype="multipart/form-data">
            @csrf
            <label for="nom_entreprise">Nom :
            <input type="text" name="nom_entreprise" id="nom_entreprise"></label>

            <label for="prix">Prix :</label>
            <input type="text" name="prix" id="prix">

            <label for="state">État :
                <select name="state" id="state">
                    <option value="D">Facture à payer</option>
                    <option value="R">Facture à rembourser</option>
                    <option value="E">Facture en attente</option>
                    <option value="P">Facture payée</option>
                </select>
            </label>

            <label for="tva">TVA :
            <input type="text" name="tva" id="tva"></label>

            <label for="date">Date :
            <input id="date" name="date" type="date"/></label>

            <label for="date_remboursement">Date remboursement:
            <input id="date_remboursement" name="date_remboursement" type="date"/></label>

            <label for="date_paiement">Date paiement :
            <input id="date_paiement" name="date_paiement" type="date"/></label>

            <br><br>

            <label for="moyen_paiement">Moyen paiement :
            <input id="moyen_paiement" name="moyen_paiement" type="text"/></label>

            <label for="personne_a_rembourser">Personne à rembourser :
            <input id="personne_a_rembourser" name="personne_a_rembourser" type="text"/></label>

            <label for="immobilisation">Imobilisation :
            <input type="checkbox" name="immobilisation" id="immobilisation" /></label>

            <br><br>

            <label for="remarque">Remarque :
            <input id="remarque" name="remarque" type="text" size="50"/></label>

            <br><br>

            <label for="categorie_id">Categories(<a href="{{route('Picsous.categorie.create')}}">+</a>)
                <select name="categorie_id" id="categorie_id">
                    @foreach($categorieFacture as $categorieFact)
                        <option value="{{$categorieFact->id}}">
                            {{$categorieFact->nom}}
                        </option>
                    @endforeach
                </select>
            </label>

            <label for="categorie_prix">Categorie prix :
            <input type="text" name="categorie_prix" id="categorie_prix"></label>

            <label for="categorie_id2">Categories(<a href="{{route('Picsous.categorie.create')}}">+</a>)
            <select name="categorie_id2" id="categorie_id2">
                @foreach($categorieFacture as $categorieFact)
                    <option value="{{$categorieFact->id}}">
                        {{$categorieFact->nom}}
                    </option>
                @endforeach
            </select>
            </label>
            <label for="categorie_prix2">Categorie prix :
            <input type="text" name="categorie_prix2" id="categorie_prix2"></label>

            <button type="submit">Créer</button>
        </form>
    </body>
</html>
