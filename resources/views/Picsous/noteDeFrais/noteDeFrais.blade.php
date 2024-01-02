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
    <a href="{{route('Picsous.notedefrais.create')}}">Ajouter une note de frais</a>
</p>
<br>
    @foreach($noteDeFrais as $noteDeFrai)
        {{dump($noteDeFrai)}}
    @endforeach
<br>
</body>
</html>
