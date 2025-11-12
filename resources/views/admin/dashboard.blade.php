@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Admin Dashboard</h2>

    <h3>Sensor Data</h3>
    <ul>
        @foreach ($sensors as $sensor)
            <li>{{ $sensor->location }} - {{ $sensor->water_level }}m</li>
        @endforeach
    </ul>

    <h3>User Reports</h3>
    <ul>
        @foreach ($reports as $report)
            <li>
                {{ $report->location }} ({{ $report->severity }})
                - {{ $report->active ? 'Active' : 'Cleared' }}
                - Verified: {{ $report->verified ? 'Yes' : 'No' }}
                <form method="POST" action="/admin/reports/{{ $report->id }}/verify" style="display:inline">@csrf<button>✔️ Verify</button></form>
                <form method="POST" action="/admin/reports/{{ $report->id }}/clear" style="display:inline">@csrf<button>🧹 Clear</button></form>
                <form method="POST" action="/admin/reports/{{ $report->id }}" style="display:inline">@csrf @method('DELETE')<button>❌ Delete</button></form>
            </li>
        @endforeach
    </ul>
</div>
@endsection
