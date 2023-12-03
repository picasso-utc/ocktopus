<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Ajouter des médias</title>
    <script>
        // Fonction asynchrone pour obtenir la durée d'une vidéo à partir d'un fichier
        async function getVideoDuration(file) {
            return new Promise((resolve, reject) => {
                // Utilisation de FileReader pour lire le fichier vidéo
                const reader = new FileReader();
                reader.onload = () => {
                    // Création d'un blob ((Binary Large Object) à partir des données lues
                    const blob = new Blob([reader.result], { type: file.type });
                    const video = document.createElement('video');

                    // Lorsque les métadonnées de la vidéo sont chargées
                    video.onloadedmetadata = () => {
                        // Libération de l'URL de l'objet vidéo
                        window.URL.revokeObjectURL(video.src);
                        // Résolution de la promesse avec la durée de la vidéo
                        resolve(video.duration);
                    };
                    //gestion des erreur
                    video.onerror = (error) => {
                        window.URL.revokeObjectURL(video.src);
                        reject(error);
                    };

                    video.src = window.URL.createObjectURL(blob);
                };

                reader.readAsArrayBuffer(file);
                reader.onerror = (error) => reject(error);
            });
        }

        async function handleChange(e) {
            const duration = await getVideoDuration(e.target.files[0]);
            document.getElementById('duree').value = duration;
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
        <input type="number"  step="0.01"  name="duree" id="duree" readonly>
    </div>

    <button type="submit">Ajouter le média</button>
</form>
</body>
</html>
