<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Éditer des médias</title>
    <script>
        async function getVideoDuration(file) {
            return new Promise((resolve, reject) => {
                const reader = new FileReader();

                reader.onload = () => {
                    const blob = new Blob([reader.result], { type: file.type });
                    const video = document.createElement('video');

                    video.onloadedmetadata = () => {
                        window.URL.revokeObjectURL(video.src);
                        resolve(video.duration);
                    };

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
<form action="{{ route('media.update', $media) }}" method="post" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <label for="name">Media :</label>
    <input type="text" name="name" id="name" value="{{ $media->name }}">

    <label for="activate">Activer :</label>
    <input type="checkbox" name="activate" id="activate" @if($media->activate) checked @endif>

    <label for="times">Temps (en secondes pour image / en nombre de fois pour vidéo) :</label>
    <input type="number" name="times" id="times" value="{{ $media->times }}">


    <label for="media_path">Fichier média :</label>
    <input type="file" name="media_path" id="media_path" onchange="handleChange(event)">

    <div id="duration_input" style="display: none;">
        <label for="duree">Durée (en secondes) :</label>
        <input type="number" step="0.01" name="duree" id="duree" value="{{ $media->duration }}" readonly>
    </div>

    <button type="submit">Éditer le média</button>
</form>
</body>
</html>
