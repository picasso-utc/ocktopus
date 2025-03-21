@php
    use \Carbon\Carbon;
@endphp
<!DOCTYPE html>
<html>
<head>
    <title>{{ $event->titre }}</title>
</head>
<body>
<p>
    Youhouuuu, vous avez réussi le shotgun pour {{ $event->titre }}. Ca sera 
    du {{ Carbon::parse($event->debut_event)->locale('fr')->translatedFormat('l j F Y à H:i') }} au 
    {{ Carbon::parse($event->fin_event)->locale('fr')->translatedFormat('l j F Y à H:i') }} 
</p>
</body>
</html>
