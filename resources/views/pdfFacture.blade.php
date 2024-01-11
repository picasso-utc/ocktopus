<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Note de Frais</title>

    <style>
        td,
        th {
            border: 1px solid rgb(190, 190, 190);
            padding: 10px;
        }
        td {
            text-align: center;
        }
        tr:nth-child(even) {
            background-color: #eee;
        }
        th[scope='col'] {
            background-color: #696969;
            color: #fff;
        }
        th[scope='row'] {
            background-color: #d7d9f2;
        }
        caption {
            padding: 10px;
            caption-side: bottom;
        }
        table {
            border-collapse: collapse;
            border: 2px solid rgb(200, 200, 200);
            letter-spacing: 1px;
            font-family: sans-serif;
            font-size: 0.8rem;
        }
        div {
            padding-bottom: 15px;
        }
        div.right {
            width: 100%;
            text-align: right;
        }
        div.center {
            width: 100%;
            text-align: center;
        }
    </style>
</head>
<body style="padding-inline: 15vw">

<div style="width: 100%; justify-content: center; align-content: center">
    <table style="width: 80%; margin: auto; color: #255ca7">
        <thead>
        <tr>
            <th rowspan="2"></th>
            <th>Facture N°{{$record->id}}</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td colspan="2"><h1><b>Facture</b></h1></td>
        </tr>
        </tbody>
    </table>
</div>

<div>
    <p>Date de facturation: {{ \Carbon\Carbon::parse($record->date_facturation)->format('d/m/Y') }} <br>
        Echéance: {{ \Carbon\Carbon::parse($record->date_facturation)->addMonth()->format('d/m/Y')}}</p>
</div>

<div class="right">
    <p>Adresse de facturation <br>
        <b>{{$record->prenom}} {{$record->nom}}</b>     <br>
        {{$record->numero_voie}}  {{$record->rue}}      <br>
        {{$record->code_postal}} {{$record->ville}}     <br>
        <a href="mailto:{{$record->email}}">{{$record->email}}</a></p>     <br>
</div>

<div>
    <p>
        Adresse émettrice :<br>
        <b>PVDC PIC’ASSO</b> <br>
        Maison des étudiants<br>
        Rue Roger Couttolenc 60200 Compiègne<br>
        <a href="mailto:team.treso.picasso@assos.utc.fr">team.treso.picasso@assos.utc.fr</a><br>
    </p>
</div>

<div class="center">
    <p><u>Objet :</u></p>
</div>

<div>
    <table style="width: 100%">
        <thead>
        <tr>
            <th><b>No</b></th>
            <th><b>Description</b></th>
            <th><b>Qté</b></th>
            <th><b>Taux TVA</b></th>
            <th><b>Prix Total HT</b></th>
            <th style="width: 15%"></th>
            <th style="width: 15%"><b>Montant total TTC</b></th>

        </tr>
        </thead>
        <tbody>
        {{$n = 0}}
        {{$total = 0}}
        @foreach($record->elementFacture as $element)
            {{$n = $n + 1}}
            <tr>
                <td><b>{{$n}}</b></td>
                <td>{{$element->description}}</td>
                <td>{{$element->quantite}}</td>
                <td>{{$element->tva}} %</td>
                <td>{{round(($element->prix_unitaire_ttc / (1 + ($element->tva/100))) * $element->quantite, 2)}} €</td>
                <td></td>
                <td>{{$element->prix_unitaire_ttc * $element->quantite}} €</td>
                {{$total =$total + ($element->prix_unitaire_ttc * $element->quantite)}}
            </tr>
        @endforeach
        <tr>
            <td colspan="5"><b>Total TTC</b></td>
            <td colspan="2"><b>{{$total}} €</b></td>
        </tr>
        </tbody>
    </table>
</div>

<div style="padding-top: 30px">
    <p>
        Paiement par virement bancaire. <br>
        IBAN : (cf document joint)
    </p>
</div>
</body>
</html>
