<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Flood Monitor</title>

    @vite(['resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
</head>

<!-- 🌊 MORE VIBRANT BACKGROUND -->
<body class="text-slate-800 min-h-screen
             bg-gradient-to-br from-emerald-100 via-cyan-100 to-sky-200">

<!-- ================= NAVBAR ================= -->
<header class="bg-gradient-to-r from-slate-900 to-slate-800 text-white shadow-lg">
    <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
        <h1 class="text-xl font-bold tracking-wide flex items-center gap-2">
            🌊 Flood Monitor
        </h1>

        <div class="flex items-center gap-3">
            <a href="{{ url('/statistics') }}"
               class="px-4 py-2 text-sm font-semibold bg-emerald-600 hover:bg-emerald-700 rounded-lg shadow">
                📊 Statistics
            </a>

            @auth
                @if(Auth::user()->is_admin)
                    <a href="{{ url('/admin/dashboard') }}"
                       class="px-4 py-2 text-sm font-semibold bg-indigo-600 hover:bg-indigo-700 rounded-lg shadow">
                        🛠 Admin
                    </a>
                @endif

                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open"
                        class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-white/10">
                        {{ Auth::user()->name }}
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                  d="M5.23 7.21a.75.75 0 011.06.02L10 11l3.71-3.77a.75.75 0 111.08 1.04l-4.24 4.3a.75.75 0 01-1.08 0L5.21 8.27a.75.75 0 01.02-1.06z"
                                  clip-rule="evenodd"/>
                        </svg>
                    </button>

                    <div x-show="open" @click.outside="open = false"
                        class="absolute right-0 mt-2 w-40 bg-white text-slate-800 rounded-lg shadow-lg z-50"
                        x-cloak>
                        <a href="{{ route('profile.show') }}"
                           class="block px-4 py-2 text-sm hover:bg-slate-100">
                            Profile
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="w-full text-left px-4 py-2 text-sm hover:bg-slate-100">
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <a href="{{ route('login') }}"
                   class="px-4 py-2 text-sm font-semibold bg-blue-600 hover:bg-blue-700 rounded-lg shadow">
                    🔑 Login
                </a>
            @endauth
        </div>
    </div>
</header>

<!-- ================= STATUS BAR ================= -->
<section class="bg-gradient-to-r from-blue-700 via-indigo-700 to-indigo-800 text-white">
    <div class="max-w-7xl mx-auto px-6 py-8 grid grid-cols-1 md:grid-cols-3 gap-6">

        <div class="bg-white/20 backdrop-blur-md rounded-xl p-5 shadow-lg border border-white/20">
            <p class="text-sm opacity-80">System Status</p>
            <p class="text-lg font-semibold text-green-300">🟢 Online</p>
        </div>

        <div class="bg-white/20 backdrop-blur-md rounded-xl p-5 shadow-lg border border-white/20">
            <p class="text-sm opacity-80">Monitoring Area</p>
            <p class="text-lg font-semibold">IIUM Campus</p>
        </div>

        <div class="bg-white/20 backdrop-blur-md rounded-xl p-5 shadow-lg border border-white/20">
            <p class="text-sm opacity-80">Flood Risk Level</p>
            <p id="riskText" class="text-lg font-semibold text-yellow-300">
                ⚠️ Moderate
            </p>
        </div>

    </div>
</section>

<!-- ================= MAP PANEL ================= -->
<div class="max-w-7xl mx-auto px-6 py-8">
    <div class="bg-white rounded-2xl shadow-xl border overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b bg-slate-50">
            <h2 class="text-lg font-semibold">🌍 IIUM Flood Monitoring Map</h2>
            <span class="text-sm text-slate-500">Live data visualization</span>
        </div>
        <div id="map" class="h-[520px]" style="min-height:520px"></div>
    </div>
</div>

<!-- ================= LEAFLET & MAP LOGIC (UNCHANGED) ================= -->
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<!-- (your entire map JS stays exactly the same here) -->
<script>
const iiumBounds = L.latLngBounds(
    [3.2420, 101.7260],
    [3.2660, 101.7480]
);

let map = L.map('map', {
    center: [3.249759542719027, 101.7342551287492],
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

setTimeout(() => map.invalidateSize(), 300);

let markers = [];
let circles = [];

// ===============================
// DYNAMIC FLOOD RISK CALCULATION
// ===============================
function calculateFloodRisk(sensors, reports) {
    let risk = 'normal';

    // Sensor-based risk
    sensors.forEach(sensor => {
        const level = parseFloat(sensor.water_level) / 100;

        if (level > 0.3) {
            risk = 'high';
        } else if (level > 0.1 && risk !== 'high') {
            risk = 'moderate';
        }
    });

    // User report-based risk
    reports.forEach(report => {
        if (['knee', 'waist', 'chest', 'head'].includes(report.severity)) {
            risk = 'high';
        } else if (report.severity === 'ankle' && risk !== 'high') {
            risk = 'moderate';
        }
    });

    return risk;
}


function loadFloodData() {
    fetch('/api/flood-data')
        .then(res => res.json())
        .then(data => {

        // =====================
        // UPDATE FLOOD RISK UI
        // =====================
        const risk = calculateFloodRisk(data.sensors, data.user_reports);
        const riskText = document.getElementById('riskText');

        if (riskText) {
            if (risk === 'high') {
                riskText.innerHTML = '🔴 High';
                riskText.className = 'text-lg font-semibold text-red-300';
            } else if (risk === 'moderate') {
                riskText.innerHTML = '⚠️ Moderate';
                riskText.className = 'text-lg font-semibold text-yellow-300';
            } else {
                riskText.innerHTML = '🟢 Normal';
                riskText.className = 'text-lg font-semibold text-green-300';
            }
        }


            markers.forEach(m => map.removeLayer(m));
            circles.forEach(c => map.removeLayer(c));
            markers = [];
            circles = [];

            data.sensors.forEach(sensor => {
                const level = parseFloat(sensor.water_level) / 100;
                const marker = L.marker([sensor.latitude, sensor.longitude])
                    .addTo(map)
                    .bindPopup(`
                        <b>Sensor</b><br>
                        Location: ${sensor.location}<br>
                        Water Level: ${level.toFixed(2)}m
                    `);
                markers.push(marker);

                if (level > 0.2) {
                    circles.push(
                        L.circle([sensor.latitude, sensor.longitude], {
                            color: 'red',
                            fillColor: '#f03',
                            fillOpacity: 0.4,
                            radius: level * 40
                        }).addTo(map)
                    );
                }
            });

            data.user_reports.forEach(report => {
                let img = report.image
                    ? `<br><img src="/storage/${report.image}"
                             onclick="openImage('/storage/${report.image}')"
                             style="width:200px;border-radius:6px;margin-top:6px;cursor:pointer;">`
                    : '';

                const marker = L.marker([report.latitude, report.longitude])
                    .addTo(map)
                    .bindPopup(`
                        <b>User Report</b><br>
                        <b>Location:</b> ${report.location}<br>
                        <b>Description:</b> ${report.description}<br>
                        <b>Severity:</b> ${report.severity}
                        ${img}
                    `);
                markers.push(marker);

                const radii = { ankle:20, knee:40, waist:70, chest:100, head:140 };
                if (radii[report.severity]) {
                    circles.push(
                        L.circle([report.latitude, report.longitude], {
                            color: 'red',
                            fillColor: '#f03',
                            fillOpacity: 0.4,
                            radius: radii[report.severity]
                        }).addTo(map)
                    );
                }
            });
        });
}

loadFloodData();
setInterval(loadFloodData, 10000);
</script>

<!-- ================= LEGEND (RESTORED) ================= -->
<div class="max-w-7xl mx-auto px-6 pb-8">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white p-4 rounded-lg shadow flex items-center gap-3">
            <span class="w-4 h-4 rounded-full bg-green-500"></span>
            <span class="text-sm">Normal Condition</span>
        </div>
        <div class="bg-white p-4 rounded-lg shadow flex items-center gap-3">
            <span class="w-4 h-4 rounded-full bg-yellow-400"></span>
            <span class="text-sm">Moderate Flood Risk</span>
        </div>
        <div class="bg-white p-4 rounded-lg shadow flex items-center gap-3">
            <span class="w-4 h-4 rounded-full bg-red-500"></span>
            <span class="text-sm">High Flood Risk</span>
        </div>
    </div>
</div>

<!-- ================= CTA (RESTORED) ================= -->
@auth
<div class="max-w-7xl mx-auto px-6 pb-14 text-center">
    <a href="{{ url('/report') }}"
       class="inline-flex items-center gap-3 px-10 py-4
              bg-gradient-to-r from-red-600 to-rose-600
              hover:from-red-700 hover:to-rose-700
              text-white text-lg font-bold
              rounded-full shadow-2xl
              transform hover:scale-105 transition">
        🚨 Report a Flood
    </a>

    <p class="mt-3 text-sm text-slate-600">
        Your report helps protect the IIUM community
    </p>
</div>
@endauth

<!-- ================= IMAGE MODAL ================= -->
<div id="imageModal"
     class="fixed inset-0 hidden items-center justify-center bg-black/80"
     style="z-index:9999;">
    <img id="modalImage"
         class="max-w-4xl max-h-[90vh] rounded-lg shadow-2xl">
</div>

<script>
function openImage(src) {
    const modal = document.getElementById('imageModal');
    const img = document.getElementById('modalImage');

    img.src = src;
    modal.style.display = 'flex';
}

// Close modal on click
document.getElementById('imageModal').addEventListener('click', () => {
    document.getElementById('imageModal').style.display = 'none';
});
</script>

</body>
</html>
