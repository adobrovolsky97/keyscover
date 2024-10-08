<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <title>{{$title}}</title>
    <meta name="description" content="{{$description}}">

    @if(!empty($meta))
        @foreach($meta as $name => $content)
           <meta property="og:{{$name}}" content="{{$content}}">
        @endforeach
    @endif

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="google-site-verification" content="VEovG_eeKiPLGsHM_vAGYayYTGZTA6aEeeR-GBIuRX0"/>
    <link rel="icon" href="{{asset('logo.png')}}">
    @vite('resources/js/app.js')
</head>
<body>
<div id="app"></div>
</body>
</html>
