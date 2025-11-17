<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Flood Monitor</title>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
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

        /* Beautiful Custom Dropdown Button */
        .user-menu-btn {
            background: transparent;
            border: none;
            color: white;
            font-size: 15px;
            padding: 8px 12px;
            border-radius: 6px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .user-menu-btn:hover {
            background: #374151;
        }

        .user-menu-dropdown {
            position: absolute;
            right: 0;
            margin-top: 8px;
            width: 160px;
            background: white;
            border-radius: 6px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
            padding: 6px 0;
            z-index: 999;
        }

        .user-menu-dropdown a,
        .user-menu-dropdown button {
            display: block;
            width: 100%;
            background: none;
            border: none;
            text-align: left;
            padding: 10px 14px;
            font-size: 14px;
            color: #111827;
            cursor: pointer;
        }

        .user-menu-dropdown a:hover,
        .user-menu-dropdown button:hover {
            background: #f3f4f6;
        }
    </style>
</head>
<body>

<header>
    <h1>🌊 Flood Monitor</h1>

    <div style="display: flex; align-items: center; gap: 10px;">
        <div class="report-container">
            <a href="{{ url('/statistics') }}" class="btn-report"
            style="background:#10b981;">📊 View Flood Statistics</a>
        </div>


        @if (Auth::check())
            {{-- Admin Dashboard Button (only for admins) --}}
            @if(Auth::user()->is_admin)
                <a href="{{ url('/admin/dashboard') }}" class="btn-login">🛠️ Admin Dashboard</a>
            @endif

            {{-- Custom Styled Dropdown (NO Jetstream classes) --}}
            <div x-data="{ open: false }" class="relative">

                <button @click="open = !open" class="user-menu-btn">
                    {{ Auth::user()->name }}
                    <svg width="16" height="16" fill="white" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M5.23 7.21a.75.75 0 011.06.02L10 11l3.71-3.77a.75.75 0 111.08 1.04l-4.24 4.3a.75.75 0 01-1.08 0L5.21 8.27a.75.75 0 01.02-1.06z"
                            clip-rule="evenodd" />
                    </svg>
                </button>

                <div x-show="open" @click.outside="open = false"
                    class="user-menu-dropdown" x-cloak>

                    <a href="{{ route('profile.show') }}">Profile</a>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit">Log Out</button>
                    </form>
                </div>

            </div>
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
                let imageHtml = '';
                if (report.image) {
                    imageHtml = `
                        <br><br>
                        <img src="/storage/${report.image}"
                            alt="Flood Image"
                            style="width: 200px; height: auto; border-radius: 6px; margin-top: 8px;">
                    `;
                }

                const marker = L.marker([lat, lng])
                    .addTo(map)
                    .bindPopup(`
                        <b>User Report</b><br>
                        <b>Location:</b> ${report.location}<br>
                        <b>Description:</b> ${report.description}<br>
                        <b>Severity:</b> ${report.severity}
                        ${imageHtml}
                    `);

                markers.push(marker);

                let severityRadius = 0;
                switch (report.severity) {
                    case 'ankle':
                        severityRadius = 20;
                        break;

                    case 'knee':
                        severityRadius = 40;
                        break;

                    case 'waist':
                        severityRadius = 70;
                        break;

                    case 'chest':
                        severityRadius = 100;
                        break;

                    case 'head':
                        severityRadius = 140;
                        break;
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
