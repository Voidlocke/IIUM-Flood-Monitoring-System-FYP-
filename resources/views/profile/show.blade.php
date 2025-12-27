@extends('layouts.app')

@section('content')
<div class="min-h-screen
            bg-gradient-to-br from-emerald-100 via-cyan-100 to-sky-200
            py-12">

    <div class="max-w-4xl mx-auto px-6 space-y-8">

        {{-- TOP ACTION BAR --}}
        <div class="flex flex-wrap gap-3">
            <a href="/"
                class="inline-flex items-center gap-2
                    bg-blue-600 hover:bg-blue-700
                    text-white px-5 py-2.5 rounded-xl
                    shadow font-semibold">
                ⬅ Back to Home
            </a>

            @if($user->is_admin)
                <a href="/admin/dashboard"
                    class="inline-flex items-center gap-2
                            bg-green-600 hover:bg-green-700
                            text-white px-5 py-2.5 rounded-xl
                            shadow font-semibold">
                    🛠️ Go to Admin Dashboard
                </a>
            @endif
        </div>

        <div class="flex items-center gap-4
                        bg-white/70 backdrop-blur
                        border-l-6 border-indigo-600
                        rounded-2xl px-6 py-4 shadow-lg mb-6">
                <h2 class="text-3xl font-extrabold text-slate-900 tracking-wide">
                    Profile
                </h2>
        </div>

        {{-- USER INFO --}}
        <div class="bg-white/80 backdrop-blur
                    p-6 rounded-2xl shadow-lg border border-white/50">
            <p><strong>Name:</strong> {{ $user->name }}</p>
            <p><strong>Email:</strong> {{ $user->email }}</p>
        </div>

        {{-- EMAIL PREFERENCES --}}
        <div class="bg-white/80 backdrop-blur
                    p-6 rounded-2xl shadow-lg border border-white/50">
            <h3 class="text-xl font-bold text-slate-800 mb-4 flex items-center gap-2">
                Email Preferences
            </h3>

            <form method="POST" action="{{ route('profile.email.preferences') }}">
                @csrf
                @method('PATCH')

                <label class="flex items-center space-x-3">
                    <input type="checkbox" name="receive_flood_alerts"
                        value="1"
                        {{ $user->receive_flood_alerts ? 'checked' : '' }}
                        class="w-4 h-4">

                    <span>
                        I want to receive flood alert emails
                    </span>
                </label>

                <button type="submit"
                    class="mt-4 bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                    Save Preferences
                </button>
            </form>
        </div>


        {{-- USER REPORTS --}}
        <div class="bg-white/80 backdrop-blur
                    p-6 rounded-2xl shadow-lg border border-white/50">
            <h3 class="text-xl font-bold text-slate-800 mb-4 flex items-center gap-2">
                Your Flood Reports
            </h3>

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
        <div class="bg-white/80 backdrop-blur
                    p-6 rounded-2xl shadow-lg border border-white/50">

            <h3 class="text-xl font-bold text-slate-800 mb-4 flex items-center gap-2">
                Your Report Statistics
            </h3>

            {{-- Severity Chart --}}
            <h4 class="font-bold mb-2">Severity Distribution</h4>
            <canvas id="severityChart"></canvas>

            {{-- Reports Over Time --}}
            <h4 class="font-bold mt-8 mb-2">Reports Over Time</h4>
            <canvas id="reportsChart"></canvas>
        </div>

        {{-- LOGOUT BUTTON --}}
        <div class="max-w-4xl mx-auto px-6">
            <div class="flex justify-end -mt-4">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="flex items-center gap-2
                            bg-red-600 hover:bg-red-700
                            text-white px-6 py-2.5
                            rounded-xl shadow-lg
                            font-semibold">
                        🚪 Log Out
                    </button>
                </form>
            </div>
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
