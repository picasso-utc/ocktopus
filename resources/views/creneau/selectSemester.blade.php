<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sélection du Semestre</title>
</head>
<body>
<div>
    <h1>Sélection du Semestre</h1>


    <form action="{{ route('creneau.create-creneaux-for-semester') }}" method="post">
        @csrf

        <label for="semestre">Choisir le Semestre :</label>
        <select name="semestre" id="semestre" required>
            <option value="automne">Automne</option>
            <option value="printemps">Printemps</option>
        </select>

        <button type="submit">Créer les Créneaux pour le semestre</button>
    </form>
</div>
</body>
</html>
