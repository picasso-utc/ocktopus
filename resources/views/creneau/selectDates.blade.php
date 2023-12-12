<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Sélection des dates</title>
</head>
<body>

    <h1>Sélection des dates</h1>

    <form action="{{ route('creneau.createCreneaux') }}" method="POST">
        @csrf

        <label for="start_date">Date de début :</label>
        <input type="date" id="start_date" name="start_date" required>

        <label for="end_date">Date de fin :</label>
        <input type="date" id="end_date" name="end_date" required>

        <button type="submit">Créer les créneaux</button>
    </form>
</body>

</html>
