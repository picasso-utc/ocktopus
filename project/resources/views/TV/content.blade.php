<!DOCTYPE HTML>
<html lang="fr">
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <title>TV</title>
</head>
<body>
@if(isset($medias[0]))
    <div class="media-container" style="display: flex; justify-content: center; align-items: center; flex: 1 1 0%; height: 100vh; background: rgb(0, 0, 0); position: absolute; inset: 0px;">
        <img src="{{ isset($medias[0]) && $medias[0]->media_type === 'Image' ? $medias[0]->media_path : '' }}" alt="{{ isset($medias[0]) ? $medias[0]->name : '' }}" style="max-width: 100vw; max-height: 100vh;
     @if (isset($medias[0]) && $medias[0]->media_type === 'Video') display: none; @endif">
        <video controls width="100%" height="auto" @if (!(isset($medias[0]) && $medias[0]->media_type === 'Video')) style="display: none;" @endif autoplay loop="{{ $medias[0]->times }}">
            <source src="{{ isset($medias[0]) && $medias[0]->media_type === 'Video' ? $medias[0]->media_path : '' }}" type="video/mp4" style="max-width: 100vw; max-height: 100vh;">
            Votre navigateur ne prend pas en charge la vidéo.
        </video>
    </div>
@else
    <div >

    </div>
@endif

<script>
    let medias = @json($medias);
    let mediaIndex = 0;
    let mediaContainer = document.querySelector('.media-container');
    let img = mediaContainer.querySelector('img');
    let video = mediaContainer.querySelector('video');
    console.log(medias)
    function showMedia() {
        let media = medias[mediaIndex];
        if (media.media_type === 'Image') {

            img.src = "{{ route('image', ['url' => '']) }}/" + media.media_path;
            img.alt = media.name;
            img.style.display = 'block';
            video.style.display = 'none';
        } else if (media.media_type === 'Video') {
            video.querySelector('source').src = "{{ route('image', ['url' => '']) }}/" + media.media_path; img.style.display = 'none';
            video.style.display = 'block';
            video.load();
        }

        mediaIndex++;

        if (mediaIndex >= medias.length) {
            mediaIndex = 0;
        }

        if (media.media_type === 'Image') {
            waitTime = media.times * 1000;
        } else if (media.media_type === 'Video') {
            waitTime = media.duree * 1000 * media.times;
        }

        setTimeout(showMedia, waitTime); //problème au niveau du temps

    }


    showMedia();

</script>
</body>
</html>
