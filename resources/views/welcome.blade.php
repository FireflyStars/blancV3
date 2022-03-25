<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon"
              type="image/png"
              href="/images/icon.png">

        <title>Blanc &copy;</title>
        <link  rel="stylesheet" href="{{ mix('css/app.css') }}">
    </head>
    <body id="app">
    </body>
    <script defer src="https://maps.googleapis.com/maps/api/js?&libraries=geometry,places&key=AIzaSyBzskMTlZlX35Ai_39lAq4lDJ7sQVWTFmY"></script>
    <script src="{{ mix('js/app.js') }}"></script>
</html>
