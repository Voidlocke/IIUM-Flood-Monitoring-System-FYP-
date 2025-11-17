@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h2 class="text-2xl font-bold mb-6">Admin Dashboard</h2>

    <a href="/admin/users/create"
        class="bg-gray-500 hover:bg-blue-600 text-white px-4 py-2 rounded shadow">
        ➕ Create Admin
    </a>

    <div class="mb-4 space-x-2">
        <a href="?filter=all" class="px-3 py-1 rounded
            {{ $filter == 'all' ? 'bg-blue-600 text-white' : 'bg-gray-200' }}">
            All
        </a>

        <a href="?filter=pending" class="px-3 py-1 rounded
            {{ $filter == 'pending' ? 'bg-yellow-500 text-white' : 'bg-gray-200' }}">
            Pending
        </a>

        <a href="?filter=approved" class="px-3 py-1 rounded
            {{ $filter == 'approved' ? 'bg-green-600 text-white' : 'bg-gray-200' }}">
            Approved
        </a>

        <a href="?filter=cleared" class="px-3 py-1 rounded
            {{ $filter == 'cleared' ? 'bg-gray-500 text-white' : 'bg-gray-200' }}">
            Cleared
        </a>

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
                            <td class="py-3 px-6 space-x-1 flex">

                                @if ($report->status === 'pending')
                                    <form method="POST" action="/admin/reports/{{ $report->id }}/approve" class="inline">
                                        @csrf
                                        <button class="bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded">
                                            ✔️ Approve
                                        </button>
                                    </form>
                                @endif

                                @if ($report->status === 'approved')
                                    <form method="POST" action="/admin/reports/{{ $report->id }}/clear" class="inline">
                                        @csrf
                                        <button class="bg-yellow-400 hover:bg-yellow-500 text-white px-2 py-1 rounded">
                                            🧹 Clear
                                        </button>
                                    </form>
                                @endif

                                <form method="POST" action="/admin/reports/{{ $report->id }}" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded">
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
