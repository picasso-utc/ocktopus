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
        <p>
            <a href="{{route('Picsous.facturerecue.create')}}">Ajouter une facture</a>
        </p>
        <br>

        <iframe src="/01HK9TDQEPPQZV26Y6DHTXCV3M.pdf" width="100%" height="500px"> </iframe>
        <a href="/01HK9TDQEPPQZV26Y6DHTXCV3M.pdf">exemple</a>.



        <table>
            <tr>
                <th>Ref.</th>
                <th>Entreprise</th>
                <th>Date</th>
                <th>Date de paiment</th>
                <th>Date de remboursement</th>
                <th>Catégorie(s)</th>
                <th>Prix TTC</th>
                <th>TVA</th>
                <th>État</th>
                <th>Personne à rembourser</th>
                <th>Perm</th>
            </tr>

            @foreach($factureRecues as $factureRecue)
                <tr>
                    <td><a href="{{route('Picsous.facturerecue.facturerecueInfo', $factureRecue) }}">Fact - {{$factureRecue->id}}</a></td>
                    <td>{{$factureRecue->nom_entreprise}}</td>
                    <td>{{date('M d, Y', strtotime($factureRecue->created_at))}}</td>
                    <td>{{$factureRecue->date_paiement}}</td>
                    <td>{{$factureRecue->date_remboursement}}</td>
                    <td>
                        @foreach($factureRecue->categoriePrix as $categoriePrix)
                            {{$categoriePrix->categorie->nom}} ({{$categoriePrix->prix}} €),
                        @endforeach
                    </td>
                    <td>{{$factureRecue->prix}} €</td>
                    <td>{{$factureRecue->tva}} €</td>
                    <td>{{$factureRecue->getStateLabel($factureRecue->state)}}</td>
                    <td>{{$factureRecue->personne_a_rembourser}}</td>
                    <td>{{$factureRecue->perm_id}}</td>
                </tr>
            @endforeach
        </table>
        <br>
    </body>
</html>
