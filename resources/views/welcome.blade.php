<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon"
              type="image/png"
              href="/images/icon.png">

        <title>Blanc &copy;</title>
        <link  rel="stylesheet" href="{{ asset('css/app.css') }}">
        <script src="https://js.stripe.com/terminal/v1/"></script>
        <!--
        <script src="https://js.stripe.com/v3/"></script>
        -->
    </head>
    <body id="app">
    </body>

    <script defer src="https://maps.googleapis.com/maps/api/js?&libraries=geometry,places&key=AIzaSyBzskMTlZlX35Ai_39lAq4lDJ7sQVWTFmY"></script>
    <script src="{{ asset('js/app.js') }}"></script>
</html>
