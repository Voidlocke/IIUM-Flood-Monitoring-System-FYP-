<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Flood</title>

    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />

    <style>
        body {
            font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #d1fae5, #cffafe, #e0f2fe);
            min-height: 100vh;
        }

        .container {
            max-width: 900px;
            margin: 50px auto;
            background: #ffffff;
            padding: 32px;
            border-radius: 16px;
            box-shadow: 0 15px 40px rgba(0,0,0,0.1);
        }

        h2 {
            text-align: center;
            color: #1f2937;
            margin-bottom: 10px;
        }

        .subtitle {
            text-align: center;
            color: #6b7280;
            margin-bottom: 30px;
            font-size: 15px;
        }

        label {
            display: block;
            margin-bottom: 6px;
            color: #374151;
            font-weight: 600;
        }

        input[type="text"],
        textarea,
        select,
        input[type="file"] {
            width: 100%;
            padding: 12px 14px;
            margin-bottom: 12px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 15px;
            box-sizing: border-box;
            transition: border 0.2s, box-shadow 0.2s;
        }

        input:focus,
        textarea:focus,
        select:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 2px rgba(59,130,246,0.2);
        }

        textarea {
            resize: vertical;
            min-height: 90px;
        }

        .section {
            margin-bottom: 20px;
        }

        .hint {
            font-size: 14px;
            color: #6b7280;
            margin-top: -14px;
            margin-bottom: 18px;
        }

        #map {
            height: 420px;
            border-radius: 14px;
            border: 1px solid #d1d5db;
            margin-bottom: 25px;
        }

        .map-hint {
            font-size: 14px;
            color: #374151;
            margin-bottom: 10px;
        }

        button {
            width: 100%;
            background: linear-gradient(135deg, #dc2626, #f43f5e);
            color: white;
            padding: 16px;
            border: none;
            border-radius: 999px;
            font-size: 17px;
            font-weight: 700;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        button:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 25px rgba(220,38,38,0.4);
        }

        @media (max-width: 640px) {
            .container {
                margin: 20px;
                padding: 24px;
            }
        }
    </style>
</head>

<body>

<div class="container">
    <a href="{{ url('/') }}"
        style="
        display:inline-block;
        margin-bottom:16px;
        color:#2563eb;
        font-weight:600;
        text-decoration:none;
        ">
        ← Back to Home
    </a>
    <h2>🚨 Report a Flood</h2>
    <p class="subtitle">
        Your report helps protect the IIUM community. Please provide accurate information.
    </p>

    <form id="reportForm" method="POST" action="/report" enctype="multipart/form-data">
        @csrf

        <!-- ================= BASIC INFO ================= -->
        <div class="section">
            <label for="location">📍 Location Name</label>
            <input type="text" name="location" id="location"
                   placeholder="Example: Mahallah Aminah entrance"
                   required>

            <label for="description">📝 Description (optional)</label>
            <textarea name="description" id="description"
                      placeholder="Describe the flood situation (road blocked, strong current, etc.)"></textarea>
        </div>

        <!-- Hidden coordinates -->
        <input type="hidden" id="lat" name="latitude">
        <input type="hidden" id="lng" name="longitude">

        <!-- ================= SEVERITY ================= -->
        <div class="section">
            <label for="severity">🌊 Water Level Severity</label>
            <select name="severity" id="severity" required>
                <option value="">-- Select water level --</option>
                <option value="ankle">Ankle Level</option>
                <option value="knee">Knee Level</option>
                <option value="waist">Waist Level</option>
                <option value="chest">Chest Level</option>
                <option value="head">Head Level</option>
            </select>

            <label for="image">📷 Upload Image (optional)</label>
            <input type="file" name="image" id="image" accept="image/*">
            <p class="hint">Clear images help authorities verify the situation faster.</p>
        </div>

        <!-- ================= MAP ================= -->
        <div class="section">
            <div style="
                background:#eff6ff;
                border:2px dashed #3b82f6;
                color:#1e3a8a;
                padding:14px;
                border-radius:12px;
                font-weight:700;
                text-align:center;
                margin-bottom:12px;
            ">
                📍 IMPORTANT: Click on the map below to choose the flood location
            </div>
            <div id="map"></div>
        </div>

        <!-- ================= SUBMIT ================= -->
        <button type="submit">Submit Flood Report</button>
    </form>
</div>

<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<script>
const iiumBounds = L.latLngBounds(
    [3.2420, 101.7260],
    [3.2660, 101.7480]
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

map.on('click', function (e) {
    if (!iiumBounds.contains(e.latlng)) {
        alert("Please select a location inside IIUM campus only.");
        return;
    }

    document.getElementById('lat').value = e.latlng.lat.toFixed(6);
    document.getElementById('lng').value = e.latlng.lng.toFixed(6);

    if (marker) {
        marker.setLatLng(e.latlng);
    } else {
        marker = L.marker(e.latlng).addTo(map);
    }
});

document.getElementById('reportForm').addEventListener('submit', function (e) {
    if (!document.getElementById('lat').value || !document.getElementById('lng').value) {
        e.preventDefault();
        alert("Please click on the map to select the flood location before submitting.");
    }
});
</script>

</body>
</html>
