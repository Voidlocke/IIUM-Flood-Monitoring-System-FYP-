<!DOCTYPE html>
<html>
<head>
    <title>Flood Statistics</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        body {
            font-family: Arial;
            background: #f7f7f7;
            margin: 20px;
        }
        .container {
            max-width: 900px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        h2 { text-align: center; }
        .back-btn {
            display: inline-block;
            padding: 10px 15px;
            background: #3490dc;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }
        table td, table th {
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }
        table th {
            background: #f3f3f3;
        }
    </style>
</head>

<body>

<div class="container">

    <a href="/" class="back-btn">⬅ Back</a>

    <h2>📊 Flood Statistics Overview</h2>

    <h3>Total Reports: {{ $totalReports }}</h3>
    <h3>Registered Sensors: {{ $sensorCount }}</h3>
    <h4>High Water Sensors: {{ $highWaterSensors }}</h4>

    <br>

    <h3>Severity Distribution</h3>
    <canvas id="severityChart"></canvas>

    <h3 style="margin-top:40px;">Reports Over Time</h3>

    <!-- RANGE SELECTOR BUTTONS -->
    <div class="mb-4" style="margin-top: 20px;">
        <a href="?range=7d" class="range-btn {{ $range=='7d' ? 'active' : '' }}">7 Days</a>
        <a href="?range=1m" class="range-btn {{ $range=='1m' ? 'active' : '' }}">1 Month</a>
        <a href="?range=6m" class="range-btn {{ $range=='6m' ? 'active' : '' }}">6 Months</a>
        <a href="?range=1y" class="range-btn {{ $range=='1y' ? 'active' : '' }}">1 Year</a>
        <a href="?range=5y" class="range-btn {{ $range=='5y' ? 'active' : '' }}">5 Years</a>
    </div>

    <style>
    .range-btn {
        padding: 8px 14px;
        background: #d1d5db;
        color: black;
        border-radius: 6px;
        text-decoration: none;
        font-size: 14px;
        margin-right: 6px;
    }
    .range-btn.active {
        background: #3490dc;
        color: white;
    }
    </style>


    <!-- Chart canvas -->
    <canvas id="reportsChart"></canvas>


    <h3 style="margin-top:40px;">Severity Table</h3>
    <table>
        <tr><th>Severity</th><th>Total Reports</th></tr>
        @foreach($severityCounts as $severity => $count)
            <tr>
                <td>{{ ucfirst($severity) }}</td>
                <td>{{ $count }}</td>
            </tr>
        @endforeach
    </table>

</div>


<script>
// Severity Chart
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
const labels = @json($reports->pluck('date'));
const values = @json($reports->pluck('total'));

new Chart(document.getElementById('reportsChart'), {
    type: 'line',
    data: {
        labels: labels,
        datasets: [{
            label: 'Flood Reports',
            data: values,
            borderWidth: 3,
            tension: 0.3
        }]
    }
});
</script>

<h3 style="margin-top:40px;">Sensor Severity Levels</h3>
<canvas id="sensorSeverityChart"></canvas>

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

<h3 style="margin-top:40px;">Sensor Water Levels Over Time</h3>
<canvas id="sensorWaterLevelChart"></canvas>

<script>
const sensorLabels = @json($sensorData->pluck('date'));
const sensorValues = @json($sensorData->pluck('avg_level'));

new Chart(document.getElementById('sensorWaterLevelChart'), {
    type: 'line',
    data: {
        labels: sensorLabels,
        datasets: [{
            label: 'Average Water Level (meters)',
            data: sensorValues,
            borderWidth: 3,
            tension: 0.3
        }]
    }
});
</script>

<h3 style="margin-top:40px;">Sensor Summary</h3>

<table>
    <tr>
        <th>Sensor Location</th>
        <th>Water Level (m)</th>
        <th>Last Updated</th>
    </tr>

    @foreach($latestSensorData as $sensor)
        <tr>
            <td>{{ $sensor->location }}</td>
            <td>{{ number_format($sensor->water_level, 2) }}</td>
            <td>{{ $sensor->created_at->format('d M Y, h:i A') }}</td>
        </tr>
    @endforeach
</table>


</body>
</html>
