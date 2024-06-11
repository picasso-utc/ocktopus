<!DOCTYPE HTML>
<html lang="fr">
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <title>TV</title>
</head>
<body>
@if(isset($medias[0]))
    <div class="media-container" style="display: flex; justify-content: center; align-items: center; flex: 1 1 0%; height: 100vh; background: rgb(0, 0, 0); position: absolute; inset: 0px;">
        <img src="{{isset($medias[0]) && $medias[0]->media_type === 'Image' ? $medias[0]->media_path : '' }}" alt="{{ isset($medias[0]) ? $medias[0]->name : '' }}" style="max-width: 100vw; max-height: 100vh;
     @if (isset($medias[0]) && $medias[0]->media_type === 'Video') display: none; @endif">
        <video muted="muted" width="100%" height="auto" @if (!(isset($medias[0]) && $medias[0]->media_type === 'Video')) style="display: none;" @endif>
            <source src="{{ isset($medias[0]) && $medias[0]->media_type === 'Video' ? $medias[0]->media_path : '' }}" type="video/mp4" style="max-width: 100vw; max-height: 100vh;">
            Votre navigateur ne prend pas en charge la vidéo.
        </video>
    </div>
@else
    <div >

    </div>
@endif

<script defer>
    let medias = @json($medias);
    let mediaIndex = 0;
    let mediaContainer = document.querySelector('.media-container');
    let img = mediaContainer.querySelector('img');
    let video = mediaContainer.querySelector('video');
    let times=0;
    video.addEventListener('loadedmetadata', () => {
        // video.play va déclencher à la fin de la video le listener ended
        video.play();
    })
    video.addEventListener('ended', async () => {
        //à chaque fois que la vidéo a terminé on décremente
        if(times>1){
            times--
            //et si c'est il faut encore jouer la vidéo, alors on la recharge
            video.load();
        }
        else{
            //sinon on passe au média suivant
            mediaIndex++;
            if (mediaIndex >= medias.length) {
                mediaIndex = 0;
            }
            //et on relance la fonction showMedia
            showMedia()
            //gerer prochain media
        };
    })

    function showMedia() {
        let media = medias[mediaIndex];
        if (media.media_type === 'Image') {
            img.src = "{{ route('image', ['url' => '']) }}/" + media.media_path;
            img.alt = media.name;
            img.style.display = 'block';
            video.style.display = 'none';
            mediaIndex++;

        } else if (media.media_type === 'Video') {
            video.querySelector('source').src = "{{ route('image', ['url' => '']) }}/" + media.media_path;
            img.style.display = 'none';
            video.style.display = 'block';
            //on initialise la variable times avec le nombre de fois que doit être joué la vidéo
            times = media.times;
            //video.load déclenche le listener loadedmetadata
            video.load();
        }

        if (mediaIndex >= medias.length) {
            mediaIndex = 0;
        }

        if (media.media_type === 'Image') {
            waitTime = media.times * 1000;
            setTimeout(showMedia, waitTime);
        }
    }
    showMedia();


</script>
</body>
</html>
