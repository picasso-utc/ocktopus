<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Ajouter des médias</title>
    <script>
        async function getVideoDuration(file) { //obtenir la durée d'un fichier
            return new Promise((resolve, reject) => {
                const reader = new FileReader();
                reader.onload = () => {
                    const media = new Audio(reader.result);
                    media.onloadedmetadata = () => resolve(media.duration);
                };
                reader.readAsDataURL(file);
                reader.onerror = (error) => reject(error);
            });
        }

        async function handleChange(e) { //Déclenche la recherche de duree  à partir du moment où un fichier est chargé
            const duration = await getVideoDuration(e.target.files[0]);
            document.getElementById('duree').value = Math.round(duration);
        }
    </script>
</head>
<body>
<form action="{{ route('media.store') }}" method="post" enctype="multipart/form-data">
    @csrf

    <label for="name">Media :</label>
    <input type="text" name="name" id="name">

    <label for="media_type">Type de média :</label>
    <select name="media_type" id="media_type">
        <option value="Image">Image</option>
        <option value="Video">Vidéo</option>
    </select>

    <label for="activate">Activer :</label>
    <input type="checkbox" name="activate" id="activate" value="1">

    <label for="times">Temps (en secondes pour image / en nombre de fois pour video) :</label>
    <input type="number" name="times" id="times" value="1">

    <label for="media_path">Fichier média :</label>
    <input type="file" name="media_path" id="media_path" onchange="handleChange(event)">

    <div id="duration_input" style="display: none;">
        <label for="duree">Durée (en secondes) :</label>
        <input type="number" name="duree" id="duree" readonly>
    </div>

    <button type="submit">Ajouter le média</button>
</form>
</body>
</html>
