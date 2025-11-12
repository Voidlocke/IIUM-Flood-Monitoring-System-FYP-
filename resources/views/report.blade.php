<!DOCTYPE html>
<html>
<head>
    <title>Report Flood</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <style>
        #map { height: 400px; }
    </style>
</head>
<body>
    <h2>Report a Flood</h2>

    <form method="POST" action="/report">
        @csrf
        <label>Location Name:</label><br>
        <input type="text" name="location" required><br><br>

        <label>Description:</label><br>
        <textarea name="description"></textarea><br><br>

        <label>Latitude:</label><br>
        <input type="text" id="lat" name="latitude" readonly><br><br>

        <label>Longitude:</label><br>
        <input type="text" id="lng" name="longitude" readonly><br><br>

        <label>Flood Severity:</label><br>
        <select name="severity" required>
            <option value="">-- Select Severity --</option>
            <option value="low">Low</option>
            <option value="moderate">Moderate</option>
            <option value="high">High</option>
            <option value="severe">Severe</option>
        </select><br><br>

        <button type="submit">Submit Report</button>
    </form>

    <h3>Select location on the map</h3>
    <div id="map"></div>

    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script>
        let map = L.map('map').setView([3.2497, 101.7342], 15); // IIUM center

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 18,
        }).addTo(map);

        let marker;

        map.on('click', function(e) {
            let lat = e.latlng.lat.toFixed(6);
            let lng = e.latlng.lng.toFixed(6);

            document.getElementById('lat').value = lat;
            document.getElementById('lng').value = lng;

            if (marker) {
                marker.setLatLng(e.latlng);
            } else {
                marker = L.marker(e.latlng).addTo(map);
            }
        });
    </script>
</body>
</html>
