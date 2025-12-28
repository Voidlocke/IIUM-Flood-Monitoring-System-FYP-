@extends('layouts.app')

@section('content')
<div class="not-prose
            min-h-screen
            bg-gradient-to-br from-emerald-100 via-cyan-100 to-sky-200">

<div class="max-w-7xl mx-auto px-6 py-10">

    <!-- ================= HEADER ================= -->
    <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div class="flex items-center gap-4
                    bg-white/70 backdrop-blur
                    border-l-6 border-gray-200
                    rounded-2xl px-6 py-4 shadow-lg">
            <span class="text-3xl">🛠</span>
            <h2 class="text-3xl font-extrabold text-slate-900 tracking-wide">
                Admin Dashboard
            </h2>
        </div>

        <!-- ACTION BUTTONS -->
        <div class="flex items-center gap-3">
            <!-- Create Admin -->
            <a href="/admin/users/create"
            class="inline-flex items-center gap-2
                    bg-gradient-to-r from-indigo-600 to-blue-600
                    hover:from-indigo-600 hover:to-blue-700
                    text-white px-5 py-3 rounded-xl
                    shadow-lg shadow-blue-500/30
                    text-sm font-semibold
                    transition transform hover:-translate-y-0.5">
                ➕ Create Admin
            </a>

            <!-- Add Sensor (more vibrant) -->
            <a href="/admin/sensors/create"
            class="inline-flex items-center gap-2
                    bg-gradient-to-r from-emerald-500 to-green-600
                    hover:from-emerald-600 hover:to-green-700
                    text-white px-6 py-3 rounded-xl
                    shadow-xl shadow-emerald-500/30
                    text-sm font-bold
                    transition transform hover:-translate-y-0.5">
                ➕ Add Sensor
            </a>
        </div>
    </div>

    <!-- ================= FILTERS ================= -->
    <div class="mb-10 flex flex-wrap gap-3">
        @php
            $btn = 'px-4 py-2 rounded-lg text-sm font-semibold transition shadow';
        @endphp

        <a href="?filter=all"
           class="{{ $btn }}
           {{ $filter=='all'
                ? 'bg-blue-600 text-white'
                : 'bg-blue-100 text-blue-700 hover:bg-blue-200' }}">
            All
        </a>

        <a href="?filter=pending"
           class="{{ $btn }}
           {{ $filter=='pending'
                ? 'bg-yellow-500 text-white'
                : 'bg-yellow-100 text-yellow-800 hover:bg-yellow-200' }}">
            Pending Verification
        </a>

        <a href="?filter=approved"
           class="{{ $btn }}
           {{ $filter=='approved'
                ? 'bg-green-600 text-white'
                : 'bg-green-100 text-green-800 hover:bg-green-200' }}">
            Ongoing
        </a>

        <a href="?filter=cleared"
           class="{{ $btn }}
           {{ $filter=='cleared'
                ? 'bg-slate-600 text-white'
                : 'bg-slate-200 text-slate-700 hover:bg-slate-300' }}">
            Resolved
        </a>
    </div>

    <!-- ================= USER REPORTS ================= -->
    <div>
        <div class="mb-6 flex items-center gap-3
                    bg-white/60 backdrop-blur
                    order-l-6 border-rose-500
                    rounded-xl px-5 py-3 shadow">
            <h3 class="text-2xl font-bold text-slate-900 tracking-wide">
                User Flood Reports
            </h3>
        </div>


        <div class="overflow-x-auto bg-white/90 backdrop-blur
                    rounded-2xl shadow-xl border border-white/60">

            <table class="min-w-full text-sm">
                <thead class="bg-slate-100 text-slate-600 uppercase text-xs">
                    <tr>
                        <th class="py-4 px-6 text-left">Image</th>
                        <th class="py-4 px-6 text-left">Location</th>
                        <th class="py-4 px-6 text-left">Severity</th>
                        <th class="py-4 px-6 text-left">Status</th>
                        <th class="py-4 px-6 text-left">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y">
                    @foreach ($reports as $report)
                    <tr class="hover:bg-slate-50 transition">

                        <!-- Image -->
                        <td class="py-4 px-6">
                            @if($report->image)
                                <img src="{{ asset('storage/' . $report->image) }}"
                                     class="w-20 rounded-lg cursor-pointer shadow"
                                     onclick="openImageModal('{{ asset('storage/' . $report->image) }}')">
                            @else
                                <span class="text-slate-400 text-xs">No image</span>
                            @endif
                        </td>

                        <td class="py-4 px-6">{{ $report->location }}</td>
                        <td class="py-4 px-6 capitalize">{{ $report->severity }}</td>

                        <!-- Status -->
                        <td class="py-4 px-6">
                            @if ($report->status === 'approved')
                                <span class="px-3 py-1 text-xs font-semibold
                                             rounded-full bg-green-100 text-green-800">
                                    Approved
                                </span>
                            @elseif ($report->status === 'pending')
                                <span class="px-3 py-1 text-xs font-semibold
                                             rounded-full bg-yellow-100 text-yellow-800">
                                    Pending
                                </span>
                            @elseif ($report->status === 'cleared')
                                <span class="px-3 py-1 text-xs font-semibold
                                             rounded-full bg-slate-200 text-slate-700">
                                    Cleared
                                </span>
                            @endif
                        </td>

                        <!-- Actions -->
                        <td class="py-4 px-6">
                            <div class="flex flex-wrap gap-2">

                                @if ($report->status === 'pending')
                                <form method="POST" action="/admin/reports/{{ $report->id }}/approve">
                                    @csrf
                                    <button class="px-4 py-2 rounded-lg
                                                   bg-blue-600 hover:bg-blue-700
                                                   text-white text-xs font-semibold">
                                        ✔ Approve
                                    </button>
                                </form>
                                @endif

                                @if ($report->status === 'approved')
                                <form method="POST" action="/admin/reports/{{ $report->id }}/clear">
                                    @csrf
                                    <button class="px-4 py-2 rounded-lg
                                                   bg-yellow-500 hover:bg-yellow-600
                                                   text-white text-xs font-semibold">
                                        🧹 Resolved
                                    </button>
                                </form>
                                @endif

                                <form method="POST" action="/admin/reports/{{ $report->id }}">
                                    @csrf
                                    @method('DELETE')
                                    <button class="px-4 py-2 rounded-lg
                                                   bg-red-600 hover:bg-red-700
                                                   text-white text-xs font-semibold">
                                        ❌ Delete
                                    </button>
                                </form>

                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

        </div>
    </div>

    <!-- ================= SENSOR DATA ================= -->
    <div class="mb-12">
        <div class="mb-6 flex items-center gap-3
                    bg-white/60 backdrop-blur
                    border-l-6 border-emerald-500
                    rounded-xl px-5 py-3 shadow">
            <h3 class="text-2xl font-bold text-slate-900 tracking-wide">
                Sensor Data
            </h3>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-5">
            @foreach ($sensors as $sensor)
                <div class="bg-white/80 backdrop-blur rounded-2xl p-5 shadow-lg border border-white/50">
                    <div class="flex items-start justify-between gap-3">
                        <h4 class="font-semibold text-slate-700">
                            {{ $sensor->location }}
                        </h4>

                        {{-- ADMIN ONLY badge --}}
                        <span class="px-3 py-1 text-xs font-bold rounded-full
                            {{ $sensor->is_active ? 'bg-green-100 text-green-800' : 'bg-slate-200 text-slate-700' }}">
                            {{ $sensor->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>

                    <p class="mt-2 text-sm text-slate-600">
                        Water Level:
                        <span class="font-bold text-slate-800">
                            {{ number_format($sensor->water_level / 100, 2) }} m
                        </span>
                    </p>

                    {{-- Toggle --}}
                    <form method="POST" action="/admin/sensors/{{ $sensor->id }}/toggle" class="mt-3">
                        @csrf
                        <button class="px-4 py-2 rounded-lg text-xs font-semibold
                            {{ $sensor->is_active ? 'bg-red-600 hover:bg-red-700 text-white' : 'bg-emerald-600 hover:bg-emerald-700 text-white' }}">
                            {{ $sensor->is_active ? 'Deactivate' : 'Activate' }}
                        </button>
                    </form>
                </div>
            @endforeach
        </div>
    </div>
</div>
</div>

<!-- ================= IMAGE MODAL ================= -->
<div id="image-modal"
     class="fixed inset-0 hidden items-center justify-center
            bg-black/80 z-50">

    <img id="image-modal-img"
         class="max-w-3xl max-h-[90vh]
                rounded-xl shadow-2xl">
</div>

<script>
function openImageModal(src) {
    document.getElementById('image-modal-img').src = src;
    document.getElementById('image-modal').style.display = 'flex';
}
document.getElementById('image-modal').addEventListener('click', function () {
    this.style.display = 'none';
});
</script>
@endsection
