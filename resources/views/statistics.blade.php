@extends('layouts.app')

@section('content')
<div class="not-prose
            min-h-screen
            bg-gradient-to-br from-emerald-100 via-cyan-100 to-sky-200">

<div class="max-w-6xl mx-auto px-6 space-y-10">

    <!-- ================= HEADER ================= -->
    <div class="flex justify-center">
    <div class="inline-flex items-center gap-4
                bg-white/70 backdrop-blur
                border-l-6 border-emerald-500
                rounded-2xl px-6 py-4 shadow-lg">
        <h2 class="text-3xl font-extrabold tracking-wide text-slate-900">
            Flood Data Overview
        </h2>
    </div>
    </div>

    <!-- ================= SUMMARY CARDS ================= -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
        <div class="bg-white/80 backdrop-blur rounded-2xl p-6 shadow border border-white/50">
            <p class="text-sm text-slate-500">Total Reports</p>
            <p class="text-3xl font-extrabold text-slate-900">
                {{ $totalReports }}
            </p>
        </div>

        <div class="bg-white/80 backdrop-blur rounded-2xl p-6 shadow border border-white/50">
            <p class="text-sm text-slate-500">Registered Sensors</p>
            <p class="text-3xl font-extrabold text-slate-900">
                {{ $sensorCount }}
            </p>
        </div>

    </div>

    <!-- ================= SEVERITY CHART ================= -->
    <div class="bg-white/80 backdrop-blur rounded-2xl p-6 shadow border border-white/50">
        <h3 class="text-xl font-bold mb-4">Severity Distribution</h3>
        <canvas id="severityChart"></canvas>
    </div>

    <!-- ================= RANGE FILTER ================= -->
    <div class="flex flex-wrap gap-3">
        @php
            $rangeBtn = 'px-4 py-2 rounded-lg text-sm font-semibold transition shadow';
        @endphp

        <a href="?range=7d" class="{{ $rangeBtn }} {{ $range=='7d' ? 'bg-blue-600 text-white' : 'bg-slate-200 hover:bg-slate-300' }}">7 Days</a>
        <a href="?range=1m" class="{{ $rangeBtn }} {{ $range=='1m' ? 'bg-blue-600 text-white' : 'bg-slate-200 hover:bg-slate-300' }}">1 Month</a>
        <a href="?range=6m" class="{{ $rangeBtn }} {{ $range=='6m' ? 'bg-blue-600 text-white' : 'bg-slate-200 hover:bg-slate-300' }}">6 Months</a>
        <a href="?range=1y" class="{{ $rangeBtn }} {{ $range=='1y' ? 'bg-blue-600 text-white' : 'bg-slate-200 hover:bg-slate-300' }}">1 Year</a>
        <a href="?range=5y" class="{{ $rangeBtn }} {{ $range=='5y' ? 'bg-blue-600 text-white' : 'bg-slate-200 hover:bg-slate-300' }}">5 Years</a>
    </div>

    <!-- ================= REPORTS OVER TIME ================= -->
    <div class="bg-white/80 backdrop-blur rounded-2xl p-6 shadow border border-white/50">
        <h3 class="text-xl font-bold mb-4">Reports Over Time</h3>
        <canvas id="reportsChart"></canvas>
    </div>

    <!-- ================= SEVERITY TABLE ================= -->
    <div class="bg-white/80 backdrop-blur rounded-2xl p-6 shadow border border-white/50">
        <h3 class="text-xl font-bold mb-4">Severity Table</h3>

        <table class="w-full text-sm">
            <thead class="bg-slate-100 text-slate-600">
                <tr>
                    <th class="px-4 py-3 text-left">Severity</th>
                    <th class="px-4 py-3 text-left">Total Reports</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @foreach($severityCounts as $severity => $count)
                <tr>
                    <td class="px-4 py-3 capitalize">{{ $severity }}</td>
                    <td class="px-4 py-3 font-semibold">{{ $count }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- ================= SENSOR SEVERITY ================= -->
    <div class="bg-white/80 backdrop-blur rounded-2xl p-6 shadow border border-white/50">
        <h3 class="text-xl font-bold mb-4">Sensor Severity Levels</h3>
        <canvas id="sensorSeverityChart"></canvas>
    </div>

    <!-- ================= SENSOR WATER LEVEL ================= -->
    <div class="bg-white/80 backdrop-blur rounded-2xl p-6 shadow border border-white/50">
        <h3 class="text-xl font-bold mb-4">Sensor Water Levels Over Time</h3>
        <canvas id="sensorWaterLevelChart"></canvas>
    </div>

    <!-- ================= SENSOR SUMMARY ================= -->
    <div class="bg-white/80 backdrop-blur rounded-2xl p-6 shadow border border-white/50">
        <h3 class="text-xl font-bold mb-4">Sensor Summary</h3>

        <table class="w-full text-sm">
            <thead class="bg-slate-100 text-slate-600">
                <tr>
                    <th class="px-4 py-3 text-left">Location</th>
                    <th class="px-4 py-3 text-left">Water Level (m)</th>
                    <th class="px-4 py-3 text-left">Last Updated</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @foreach($latestSensorData as $sensor)
                <tr>
                    <td class="px-4 py-3">{{ $sensor->location }}</td>
                    <td class="px-4 py-3 font-semibold">
                        {{ number_format($sensor->water_level, 2) }}
                    </td>
                    <td class="px-4 py-3">
                        {{ $sensor->created_at->format('d M Y, h:i A') }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>
</div>

@endsection

{{-- ================= CHART SCRIPTS ================= --}}

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush

@push('scripts')
<script>
new Chart(document.getElementById('severityChart'), {
    type: 'bar',
    data: {
        labels: {!! json_encode($severityCounts->keys()) !!},
        datasets: [{
            label: 'Reports',
            data: {!! json_encode($severityCounts->values()) !!},
            borderWidth: 1
        }]
    }
});
</script>

<script>
new Chart(document.getElementById('reportsChart'), {
    type: 'line',
    data: {
        labels: @json($reports->pluck('date')),
        datasets: [{
            label: 'Flood Reports',
            data: @json($reports->pluck('total')),
            borderWidth: 3,
            tension: 0.3
        }]
    }
});
</script>

<script>
new Chart(document.getElementById('sensorSeverityChart'), {
    type: 'bar',
    data: {
        labels: {!! json_encode(array_keys($sensorSeverityCounts)) !!},
        datasets: [{
            label: 'Sensor Readings',
            data: {!! json_encode(array_values($sensorSeverityCounts)) !!},
            borderWidth: 1
        }]
    }
});
</script>

<script>
new Chart(document.getElementById('sensorWaterLevelChart'), {
    type: 'line',
    data: {
        labels: @json($sensorData->pluck('date')),
        datasets: [{
            label: 'Average Water Level (meters)',
            data: @json($sensorData->pluck('avg_level')),
            borderWidth: 3,
            tension: 0.3
        }]
    }
});
</script>
@endpush
