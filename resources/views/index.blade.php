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

    <script type="text/javascript">
        (function(c,l,a,r,i,t,y){
            c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};
            t=l.createElement(r);t.async=1;t.src="https://www.clarity.ms/tag/"+i;
            y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);
        })(window, document, "clarity", "script", "sb4da4yw92");
    </script>

    @vite('resources/js/app.js')
</head>
<body>
<div id="app"></div>
</body>
</html>
