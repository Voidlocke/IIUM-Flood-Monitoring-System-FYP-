@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

        {{-- USER INFO --}}
        <div class="bg-white p-6 shadow sm:rounded-lg">
            <h2 class="text-2xl font-bold mb-4">Your Profile</h2>

            <p><strong>Name:</strong> {{ $user->name }}</p>
            <p><strong>Email:</strong> {{ $user->email }}</p>
        </div>

        {{-- USER REPORTS --}}
        <div class="bg-white p-6 shadow sm:rounded-lg">
            <h3 class="text-lg font-semibold mb-4">Your Flood Reports</h3>

            @if($reports->count() > 0)
                <table class="w-full text-left border-collapse">
                    <tr>
                        <th class="border-b p-2">Location</th>
                        <th class="border-b p-2">Severity</th>
                        <th class="border-b p-2">Date</th>
                    </tr>

                    @foreach($reports as $report)
                    <tr>
                        <td class="border-b p-2">{{ $report->location }}</td>
                        <td class="border-b p-2">{{ ucfirst($report->severity) }}</td>
                        <td class="border-b p-2">{{ $report->created_at->format('d M Y, h:i A') }}</td>
                    </tr>
                    @endforeach
                </table>
            @else
                <p class="text-gray-600">You have not submitted any reports yet.</p>
            @endif
        </div>

        {{-- USER STATISTICS --}}
        <div class="bg-white p-6 shadow sm:rounded-lg mt-8">
            <h3 class="text-xl font-semibold mb-4">Your Report Statistics</h3>

            {{-- Severity Chart --}}
            <h4 class="font-bold mb-2">Severity Distribution</h4>
            <canvas id="severityChart"></canvas>

            {{-- Reports Over Time --}}
            <h4 class="font-bold mt-8 mb-2">Reports Over Time</h4>
            <canvas id="reportsChart"></canvas>
        </div>

        {{-- BUTTONS --}}
        <div class="bg-white p-6 shadow sm:rounded-lg space-y-4">

            <a href="/" class="inline-block bg-blue-500 hover:bg-blue-600 text-grey px-4 py-2 rounded">
                ⬅ Back to Home
            </a>

            @if($user->is_admin)
            <a href="/admin/dashboard"
               class="inline-block bg-green-600 hover:bg-green-700 text-grey px-4 py-2 rounded">
               🛠️ Go to Admin Dashboard
            </a>
            @endif

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">
                    Log Out
                </button>
            </form>
        </div>

    </div>
</div>

{{-- Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
/* ---------------- SEVERITY CHART ---------------- */
const severityLabels = @json($severityCounts->keys()->map(fn($s) => ucfirst($s)));
const severityValues = @json($severityCounts->values());

new Chart(document.getElementById('severityChart'), {
    type: 'bar',
    data: {
        labels: severityLabels,
        datasets: [{
            label: 'Reports',
            data: severityValues,
            backgroundColor: '#3490dc'
        }]
    }
});

/* ---------------- REPORTS OVER TIME CHART ---------------- */
const reportLabels = @json($reportsOverTime->pluck('date'));
const reportValues = @json($reportsOverTime->pluck('total'));

new Chart(document.getElementById('reportsChart'), {
    type: 'line',
    data: {
        labels: reportLabels,
        datasets: [{
            label: 'Reports Over Time',
            data: reportValues,
            borderColor: '#e3342f',
            borderWidth: 2,
            tension: 0.3
        }]
    }
});
</script>

@endsection
