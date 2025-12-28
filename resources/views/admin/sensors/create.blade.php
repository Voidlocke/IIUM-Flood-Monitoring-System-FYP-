@extends('layouts.app')

@section('content')
<div class="not-prose min-h-screen bg-gradient-to-br from-emerald-100 via-cyan-100 to-sky-200">
    <div class="max-w-4xl mx-auto px-6 py-10">

        <div class="mb-6 flex items-center gap-4 bg-white/70 backdrop-blur border-l-6 border-emerald-500 rounded-2xl px-6 py-4 shadow-lg">
            <h2 class="text-3xl font-extrabold tracking-wide text-slate-900">Add Sensor</h2>
        </div>

        <div class="bg-white/80 backdrop-blur rounded-2xl p-6 shadow border border-white/50">
            <form method="POST" action="/admin/sensors">
                @csrf

                <label class="block font-semibold text-slate-700 mb-2">📍 Location Name</label>
                <input type="text" name="location" required
                       class="w-full mb-4 px-4 py-3 rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-400"
                       placeholder="Example: Mahallah Aminah Entrance">

                <input type="hidden" id="lat" name="latitude">
                <input type="hidden" id="lng" name="longitude">

                <div class="bg-blue-50 border-2 border-dashed border-blue-400 text-blue-900 font-bold text-center p-4 rounded-xl mb-4">
                    📍 Click on the map to set sensor location
                </div>

                <div id="map" class="h-[420px] rounded-xl border border-slate-200 mb-6"></div>

                <button type="submit"
                        class="w-full bg-green-500 hover:bg-green-600 text-white font-bold py-4 rounded-full shadow-lg">
                    ➕ Add Sensor
                </button>
            </form>
        </div>

        {{-- Optional Back button --}}
        <div class="mt-5 flex justify-start">
            <a href="/admin/dashboard"
               class="inline-flex items-center gap-2 px-5 py-2.5
                      bg-blue-600 hover:bg-blue-700
                      text-white rounded-xl shadow
                      font-semibold
                      transition">
                ⬅️ Back to Dashboard
            </a>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
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
</script>
@endpush
