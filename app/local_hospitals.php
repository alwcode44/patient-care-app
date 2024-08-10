<!DOCTYPE html>
<html>
<head>
    <title>Nearby Hospitals</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <style>
        #map {
            height: 500px;
        }
    </style>
</head>
<body>
    <h2>Nearby Hospitals</h2>
    <div id="map"></div>

    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script>
        var map = L.map('map').setView([51.505, -0.09], 13); // Default center and zoom level

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        // Function to add markers
        function addMarker(lat, lng, name) {
            L.marker([lat, lng]).addTo(map)
                .bindPopup(name)
                .openPopup();
        }

        // Dummy data for hospitals (replace with actual data)
        var hospitals = [
            { name: 'Hospital A', lat: 51.5, lng: -0.1 },
            { name: 'Hospital B', lat: 51.51, lng: -0.11 },
            { name: 'Hospital C', lat: 51.49, lng: -0.08 }
            // Add more hospitals as needed
        ];

        // Loop through hospitals and add markers
        hospitals.forEach(function(hospital) {
            addMarker(hospital.lat, hospital.lng, hospital.name);
        });

        // Use geolocation to center map on user's location (if supported)
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                var userLocation = {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude
                };
                map.setView(userLocation, 13);
            });
        }
    </script>
</body>
</html>
