<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Map View</title>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBYQgniUBzWbYGJEDEZfpUOO5tNuZODkgU&callback=initMap&v=beta&libraries=marker&loading=async" async defer></script>
    <script src="js/map.js" defer></script>
    <style>
        #map {
            height: 100%;
            width: 100%;
        }
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }
    </style>
</head>
<body>
    <div id="map"></div>
</body>
</html>
