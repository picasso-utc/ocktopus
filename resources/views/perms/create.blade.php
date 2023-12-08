<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script>
        // Obtenez la case à cocher et le groupe de champ d'e-mail
        let assoCheckbox = document.getElementById('assoCheckbox');
        let mailAssoGroup = document.getElementById('mailAssoGroup');

        // Cachez le groupe de champ d'e-mail au début si la case à cocher n'est pas cochée
        if (!assoCheckbox.checked) {
            mailAssoGroup.style.display = 'none';
        }

        // Ajoutez un écouteur d'événements à la case à cocher pour afficher/masquer le champ d'e-mail
        assoCheckbox.addEventListener('change', function () {
            if (assoCheckbox.checked) {
                mailAssoGroup.style.display = 'block';
            } else {
                mailAssoGroup.style.display = 'none';
            }
        });
    </script>
    <title>Demander une perm</title>
    @section('content')
        <div class="container">
            <h1>Demande de perm</h1>

            <form method="POST" action="{{ route('perm.store') }}">
                @csrf

                <div class="form-group">
                    <label for="nom">Nom :</label>
                    <input type="text" name="nom" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="asso">Asso :</label>
                    <input type="checkbox" name="asso" id="assoCheckbox" checked>
                </div>

                <div class="form-group" id="mailAssoGroup">
                    <label for="mailAsso">Mail de l'asso :</label>
                    <input type="email" name="mailAsso" class="form-control">
                </div>

                <div class="form-group">
                    <label for="theme">Thème :</label>
                    <input type="text" name="theme" class="form-control" required>
                </div>

                <p>Responsable 1 :</p>
                <div class="form-group">
                    <label for="nom_resp">Nom :</label>
                    <input type="text" name="nom_resp" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="mail_resp">Mail :</label>
                    <input type="email" name="mail_resp" class="form-control" required>
                </div>
                <p>Responsable 2 :</p>
                <div class="form-group">
                    <label for="nom_resp2">Nom :</label>
                    <input type="text" name="nom_resp2" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="mail_resp">Mail :</label>
                    <input type="email" name="mail_resp2" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="description">Description :</label>
                    <textarea name="description" class="form-control"></textarea>
                </div>

                <div class="form-group">
                    <label for="periode">Période :</label>
                    <textarea name="periode" class="form-control"></textarea>
                </div>


                <div class="form-group">
                    <label for="membres">Membres :</label>
                    <textarea name="membres" class="form-control"></textarea>
                </div>

                <div class="form-group">
                    <label for="ambiance">Ambiance (entre 1 et 5):</label>
                    <input type="number" name="ambiance" class="form-control" min="1" max="5" value="1" required>
                </div>


                <button type="submit" class="btn btn-primary">Créer</button>
            </form>
        </div>
</head>
<body>

</body>
</html>
