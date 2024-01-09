<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Les factures reçues</title>
</head>
<body>
    <h1><a href="{{route('Picsous.facturerecue')}}">Factures Reçues</a> > {{$factureRecue->id}} {{$factureRecue->nom_entreprise}}</h1>

    <form action="{{route('Picsous.facturerecue.edit', $factureRecue)}}">
        <button type="submit">Editer</button>
    </form>
    <br>

    <a href="{{$factureRecue->pdf_path}}">exemple</a>

    <iframe src="{{$factureRecue->pdf_path}}" width="100%" height="500px"> </iframe>

    <br>    <br>


    <form action="{{route('Picsous.facturerecue.destroy', $factureRecue)}}"  method ="POST">
        @csrf
        @method('DELETE')
        <button type="submit">Supprimer</button>
    </form>

    <h1>Détails</h1>
    <table>
        <tr>
            <th>Nom entreprise</th>
            <th>Date</th>
            <th>Prix TTC</th>
            <th>TVA</th>
            <th>Catégorie(s)</th>
            <th>Date de paiment</th>
            <th>Personne à rembourser</th>
            <th>Date de remboursement</th>
        </tr>

            <tr>
                <td>{{$factureRecue->destinataire}}</td>
                <td>{{date('M d, Y', strtotime($factureRecue->created_at))}}</td>
                <td>{{$factureRecue->prix}} €</td>
                <td>{{$factureRecue->tva}} €</td>
                <td>
                    @foreach($factureRecue->categoriePrix as $categoriePrix)
                        {{$categoriePrix->categorie->nom}} ({{$categoriePrix->prix}} €),
                    @endforeach
                </td>
                <td>{{$factureRecue->date_paiement}}</td>
                <td>{{$factureRecue->personne_a_rembourser}}</td>
                <td>{{$factureRecue->date_remboursement}}</td>
            </tr>
    </table>
    <br>

    <h1>Informations complémentaires</h1>
    <table>
        <tr>
            <th>Moyen de paiement</th>
            <th>État</th>
            <th>Immobilisation</th>
            <th>Perm</th>
        </tr>

        <tr>
            <td>{{$factureRecue->moyen_paiement}}</td>
            <td>{{$factureRecue->getStateLabel($factureRecue->state)}}</td>
            <td><input type="checkbox" style="pointer-events: none; cursor: not-allowed;"/></td>
            <td>--</td>
        </tr>
    </table>
</body>
</html>
