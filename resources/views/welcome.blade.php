<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Flood Monitor</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: #f7f7f7;
        }
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #1f2937;
            color: white;
            padding: 10px 20px;
        }
        header a {
            text-decoration: none;
            color: white;
            padding: 8px 15px;
            border-radius: 5px;
            transition: background 0.3s;
        }
        header a:hover {
            opacity: 0.9;
        }
        #map {
            height: 500px;
            margin: 20px auto;
            max-width: 1200px;
            border-radius: 8px;
            border: 1px solid #ccc;
        }
        .btn-login, .btn-report {
            background: #3490dc;
            color: white;
            text-decoration: none;
            padding: 12px 20px;
            font-size: 16px;
            border-radius: 6px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            display: inline-block;
            transition: transform 0.2s, opacity 0.2s;
        }
        .btn-login:hover, .btn-report:hover {
            transform: translateY(-2px);
            opacity: 0.9;
        }
        .report-container {
            text-align: center;
            margin: 20px 0 40px 0;
        }
        #success-message {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #d4edda;
            color: #155724;
            padding: 15px 20px;
            border: 1px solid #c3e6cb;
            border-radius: 5px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.2);
            transition: opacity 1s ease;
            z-index: 999;
        }
    </style>
</head>
<body>

<header>
    <div>
        <h1>🌊 Flood Monitor</h1>
    </div>
    <div>
        @if(Auth::check())
            @if(Auth::user()->is_admin)
                <a href="{{ url('/admin/dashboard') }}" class="btn-login">🛠️ Admin Dashboard</a>
            @else
                <span>Hello, {{ Auth::user()->name }}</span>
            @endif
        @else
            <a href="{{ route('login') }}" class="btn-login">🔑 Login</a>
        @endif
    </div>
</header>

@if (session('success'))
    <div id="success-message">{{ session('success') }}</div>
    <script>
        setTimeout(() => {
            const msg = document.getElementById('success-message');
            if (msg) {
                msg.style.opacity = '0';
                setTimeout(() => msg.style.display = 'none', 1000);
            }
        }, 2000);
    </script>
@endif

<div id="map"></div>

@if(Auth::check())
    <div class="report-container">
        <a href="{{ url('/report') }}" class="btn-report">📝 Report a Flood</a>
    </div>
@endif

<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
let map = L.map('map').setView([3.249759542719027, 101.7342551287492], 17);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 18,
}).addTo(map);

let markers = [];
let circles = [];

function loadFloodData() {
    fetch('/api/flood-data')
        .then(res => res.json())
        .then(data => {
            markers.forEach(marker => map.removeLayer(marker));
            circles.forEach(circle => map.removeLayer(circle));
            markers = [];
            circles = [];

            data.sensors.forEach(sensor => {
                const lat = sensor.latitude;
                const lng = sensor.longitude;
                const waterLevel = parseFloat(sensor.water_level) / 100;

                const marker = L.marker([lat,lng])
                    .addTo(map)
                    .bindPopup(`<b>Sensor</b><br>Location: ${sensor.location}<br>Water Level: ${waterLevel.toFixed(2)}m`);
                markers.push(marker);

                if (waterLevel > 0.2) {
                    const circle = L.circle([lat, lng], {
                        color: 'red',
                        fillColor: '#f03',
                        fillOpacity: 0.4,
                        radius: waterLevel * 40
                    }).addTo(map);
                    circles.push(circle);
                }
            });

            data.user_reports.forEach(report => {
                const lat = report.latitude;
                const lng = report.longitude;
                const marker = L.marker([lat,lng])
                    .addTo(map)
                    .bindPopup(`<b>User Report</b><br>Location: ${report.location}<br>Description: ${report.description}<br>Severity: ${report.severity}`);
                markers.push(marker);

                let severityRadius = 0;
                switch (report.severity) {
                    case 'low': severityRadius = 25; break;
                    case 'moderate': severityRadius = 50; break;
                    case 'high': severityRadius = 75; break;
                    case 'severe': severityRadius = 100; break;
                }

                if (severityRadius > 0) {
                    const circle = L.circle([lat, lng], {
                        color: 'red',
                        fillColor: '#f03',
                        fillOpacity: 0.4,
                        radius: severityRadius
                    }).addTo(map);
                    circles.push(circle);
                }
            });

            const iium = L.marker([3.249759542719027, 101.7342551287492])
                .addTo(map)
                .bindPopup('IIUM - Main Campus');
            markers.push(iium);
        })
        .catch(err => console.error("Failed to load flood data", err));
}

loadFloodData();
setInterval(loadFloodData, 10000);
</script>

</body>
</html>
