<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Flood</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: #f3f4f6;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 800px;
            margin: 40px auto;
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        h2 {
            text-align: center;
            color: #1f2937;
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 6px;
            color: #374151;
            font-weight: 500;
        }

        input[type="text"], textarea, select {
            width: 100%;
            padding: 10px 12px;
            margin-bottom: 20px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 16px;
            box-sizing: border-box;
        }

        textarea {
            resize: vertical;
        }

        button {
            background: #3490dc;
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        button:hover {
            background: #2f9e5f;
        }

        #map {
            height: 400px;
            margin-bottom: 20px;
            border-radius: 12px;
            border: 1px solid #d1d5db;
        }

        @media (max-width: 600px) {
            .container {
                margin: 20px;
                padding: 20px;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Report a Flood</h2>

    <form id="reportForm" method="POST" action="/report" enctype="multipart/form-data">
        @csrf
        <label for="location">Location Name</label>
        <input type="text" name="location" id="location" placeholder="Enter location" required>

        <label for="description">Description</label>
        <textarea name="description" id="description" placeholder="Optional details"></textarea>


        <input type="hidden" id="lat" name="latitude" readonly>

        <input type="hidden" id="lng" name="longitude" readonly>

        <label for="severity">Water Level</label>
        <select name="severity" id="severity" required>
            <option value="">-- Select Water level --</option>
            <option value="ankle">Ankle Level</option>
            <option value="knee">Knee Level</option>
            <option value="waist">Waist Level</option>
            <option value="chest">Chest Level</option>
            <option value="head">Head Level</option>
        </select>

        <label for="image">Upload Image</label>
        <input type="file" name="image" id="image" accept="image/*">

        <p id="mapHelp" style="margin: 10px 0; color:#374151; font-size: 14px;">
            📍 Click on the map to choose the flood location.
        </p>

        <div id="map"></div>

        <button type="submit">Submit Report</button>
    </form>
</div>

<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
    const iiumBounds = L.latLngBounds(
        [3.2420, 101.7260], // South-West
        [3.2660, 101.7480]  // North-East
    );

    let map = L.map('map', {
        center: [3.2497, 101.7342],
        zoom: 17,
        minZoom: 16,
        maxZoom: 18,
        maxBounds: iiumBounds,
        maxBoundsViscosity: 1.0
    });

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        minZoom: 16,
        maxZoom: 18,
    }).addTo(map);

    let marker;

    map.on('click', function(e) {
        // Extra safety: prevent clicks outside IIUM
        if (!iiumBounds.contains(e.latlng)) {
            alert("Please select a location inside IIUM campus only.");
            return;
        }


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

<script>
document.getElementById('reportForm').addEventListener('submit', function (e) {
    const lat = document.getElementById('lat').value;
    const lng = document.getElementById('lng').value;

    if (!lat || !lng) {
        e.preventDefault();
        alert("Please click on the map to select a flood location before submitting.");
    }
});
</script>

</body>
</html>
