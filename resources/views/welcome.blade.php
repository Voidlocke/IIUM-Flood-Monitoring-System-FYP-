<!DOCTYPE html>
<html>
<head>
    <title>Flood Monitor</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <style>
        #map { height: 600px; }
    </style>
</head>
<body>

    @if(Auth::check())
        @if(Auth::user()->is_admin)
            <!-- Admin sees a link to admin dashboard instead of login -->
            <a href="{{ url('/admin/dashboard') }}"
            style="padding: 10px 20px; background: #38c172; color: white; text-decoration: none; border-radius: 5px; margin-left: 10px;">
            🛠️ Admin Dashboard
            </a>
        @else
            <!-- Normal logged-in user: you can show nothing or a welcome message -->
            <span style="margin-left: 10px; color: gray;">Hello, {{ Auth::user()->name }}</span>
        @endif
    @else
        <!-- Guest sees login button -->
        <a href="{{ route('login') }}"
        style="padding: 10px 20px; background: #38c172; color: white; text-decoration: none; border-radius: 5px; margin-left: 10px;">
        🔑 Login
        </a>
    @endif


    <h2>Flood Map</h2>
    <div id="map" style="height: 500px;"></div>

    @if (session('success'))
        <div id="success-message" style="background: #d4edda; color: #155724; padding: 10px; border: 1px solid #c3e6cb; margin-bottom: 15px; transition: opacity 1s ease;">
        {{ session('success') }}
        </div>

        <script>
            setTimeout(() => {
                const msg = document.getElementById('success-message');
                if (msg) {
                    msg.style.opacity = '0';
                    setTimeout(() => msg.style.display = 'none', 1000); // hide after fade
                }
            }, 1000); // wait 1 seconds before starting fade
        </script>
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
                // Remove old markers
                markers.forEach(marker => map.removeLayer(marker));
                circles.forEach(circle => map.removeLayer(circle));
                markers = [];
                circles = [];

                // Sensor markers
                data.sensors.forEach(sensor => {
                    const lat = sensor.latitude;
                    const lng = sensor.longitude;
                    const waterLevel = parseFloat(sensor.water_level) / 100; // cm → m

                    const marker = L.marker([lat,lng])
                        .addTo(map)
                        .bindPopup(`<b>Sensor</b><br>Location: ${sensor.location}<br>Water Level: ${waterLevel.toFixed(2)}m`);
                    markers.push(marker);

                    // Draw red circle if danger (e.g. > 50 cm)
                    if (waterLevel > 0.2) {
                        const dangerRadius = waterLevel * 40; // radius in meters
                        const circle = L.circle([lat, lng], {
                            color: 'red',
                            fillColor: '#f03',
                            fillOpacity: 0.4,
                            radius: dangerRadius
                        }).addTo(map);
                        circles.push(circle);
                    }

                });

                // User reports
                data.user_reports.forEach(report => {
                    const lat = report.latitude;
                    const lng = report.longitude;

                    const marker = L.marker([lat,lng])
                        .addTo(map)
                        .bindPopup(`<b>User Report</b><br>Location: ${report.location}<br>Description: ${report.description}<br>Severity: ${report.severity}`);
                    markers.push(marker);

                    // Draw danger circle based on severity
                    let severityRadius = 0;

                    switch (report.severity) {
                        case 'low':
                            severityRadius = 25;
                            break;
                        case 'moderate':
                            severityRadius = 50;
                            break;
                        case 'high':
                            severityRadius = 75;
                            break;
                        case 'severe':
                            severityRadius = 100;
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

                // Always show main campus
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


    <a href="{{ url('/report') }}" style="padding: 10px 20px; background: #3490dc; color: white; text-decoration: none; border-radius: 5px;">
        📝 Report a Flood
    </a>

</body>
</html>
