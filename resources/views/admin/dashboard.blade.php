@extends('layouts.app')

@section('content')
<div class="not-prose">
<div class="max-w-7xl mx-auto px-4 py-6">
    <h2 class="text-2xl font-bold mb-6">Admin Dashboard</h2>

    {{-- TOP ACTION BAR --}}
    <div class="mb-6 flex flex-col gap-4">

        {{-- Create Admin --}}
        <div class="w-fit">
            <a href="/admin/users/create"
            class="inline-flex items-center gap-2 bg-gray-700 hover:bg-gray-800 text-white px-4 py-2 rounded-md shadow text-sm font-semibold">
                ➕ Create Admin
            </a>
        </div>

        {{-- Filters --}}
        <div class="flex flex-wrap gap-2">
            {{-- All --}}
            <a href="?filter=all"
            class="px-4 py-2 rounded-md text-sm font-semibold transition
            {{ $filter=='all'
                    ? 'bg-blue-600 text-white'
                    : 'bg-blue-100 text-blue-700 hover:bg-blue-200' }}">
                All
            </a>

            {{-- Pending --}}
            <a href="?filter=pending"
            class="px-4 py-2 rounded-md text-sm font-semibold transition
            {{ $filter=='pending'
                    ? 'bg-yellow-500 text-white'
                    : 'bg-yellow-100 text-yellow-800 hover:bg-yellow-200' }}">
                Pending
            </a>

            {{-- Approved --}}
            <a href="?filter=approved"
            class="px-4 py-2 rounded-md text-sm font-semibold transition
            {{ $filter=='approved'
                    ? 'bg-green-600 text-white'
                    : 'bg-green-100 text-green-800 hover:bg-green-200' }}">
                Approved
            </a>

            {{-- Cleared --}}
            <a href="?filter=cleared"
            class="px-4 py-2 rounded-md text-sm font-semibold transition
            {{ $filter=='cleared'
                    ? 'bg-gray-600 text-white'
                    : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                Cleared
            </a>
        </div>
    </div>


    <!-- Sensor Data -->
    <div class="mb-8">
        <h3 class="text-xl font-semibold mb-4">Sensor Data</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
            @foreach ($sensors as $sensor)
                <div class="bg-white shadow rounded-lg p-4">
                    <h4 class="font-medium">{{ $sensor->location }}</h4>
                    <p>Water Level: <span class="font-semibold">{{ $sensor->water_level }}m</span></p>
                </div>
            @endforeach
        </div>
    </div>

    <!-- User Reports -->
    <div>
        <h3 class="text-xl font-semibold mb-4">User Reports</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white shadow rounded-lg overflow-hidden">
                <thead class="bg-gray-100 text-gray-600 uppercase text-sm leading-normal">
                    <tr>
                        <th class="py-3 px-6 text-left">Image</th>
                        <th class="py-3 px-6 text-left">Location</th>
                        <th class="py-3 px-6 text-left">Severity</th>
                        <th class="py-3 px-6 text-left">Status</th>
                        <th class="py-3 px-6 text-left">Actions</th>
                    </tr>
                </thead>

                <tbody class="text-gray-700 text-sm">
                    @foreach ($reports as $report)
                        <tr class="border-b border-gray-200 hover:bg-gray-50">

                            <!-- Image -->
                            <td class="py-3 px-6">
                                @if($report->image)
                                    <img src="/storage/{{ $report->image }}"
                                        class="w-20 h-auto rounded cursor-pointer"
                                        onclick="openImageModal('/storage/{{ $report->image }}')">
                                @else
                                    <span class="text-gray-400 text-xs">No image</span>
                                @endif
                            </td>

                            <!-- Location -->
                            <td class="py-3 px-6">{{ $report->location }}</td>

                            <!-- Severity -->
                            <td class="py-3 px-6 capitalize">{{ $report->severity }}</td>

                            <!-- Status -->
                            <td class="py-3 px-6">
                                @if ($report->status === 'approved')
                                    <span class="px-2 inline-flex text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                        Approved
                                    </span>
                                @elseif ($report->status === 'pending')
                                    <span class="px-2 inline-flex text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        Pending
                                    </span>
                                @elseif ($report->status === 'cleared')
                                    <span class="px-2 inline-flex text-xs font-semibold rounded-full bg-gray-200 text-gray-600">
                                        Cleared
                                    </span>
                                @endif
                            </td>

                            <!-- Actions -->
                            <td class="py-3 px-6">
                                <div class="flex flex-wrap gap-2">

                                @if ($report->status === 'pending')
                                    <form method="POST" action="/admin/reports/{{ $report->id }}/approve">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center justify-center w-28 px-4 py-2 text-sm font-semibold text-white rounded"
                                        style="background-color:#2563eb"
                                        onmouseover="this.style.backgroundColor='#1d4ed8'"
                                        onmouseout="this.style.backgroundColor='#2563eb'">
                                            ✔️ Approve
                                        </button>
                                    </form>
                                @endif

                                @if ($report->status === 'approved')
                                    <form method="POST" action="/admin/reports/{{ $report->id }}/clear">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center justify-center w-28 px-4 py-2 text-sm font-semibold text-white rounded"
                                        style="background-color:#f59e0b"
                                        onmouseover="this.style.backgroundColor='#d97706'"
                                        onmouseout="this.style.backgroundColor='#f59e0b'">
                                            🧹 Clear
                                        </button>
                                    </form>
                                @endif

                                <form method="POST" action="/admin/reports/{{ $report->id }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center justify-center w-28 px-4 py-2 text-sm font-semibold text-white rounded"
                                    style="background-color:#dc2626"
                                    onmouseover="this.style.backgroundColor='#b91c1c'"
                                    onmouseout="this.style.backgroundColor='#dc2626'">
                                        ❌ Delete
                                    </button>
                                </form>

                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

        </div>
    </div>
</div>
</div>
@endsection

<!-- Image Preview Modal -->
<div id="image-modal"
     class="fixed inset-0 bg-black bg-opacity-70 hidden items-center justify-center z-50">

    <img id="image-modal-img" src="" class="max-w-3xl max-h-[90vh] rounded-lg shadow-lg">

</div>

<script>
function openImageModal(src) {
    document.getElementById('image-modal-img').src = src;
    document.getElementById('image-modal').style.display = 'flex';
}

document.getElementById('image-modal').addEventListener('click', function() {
    this.style.display = 'none';
});
</script>
