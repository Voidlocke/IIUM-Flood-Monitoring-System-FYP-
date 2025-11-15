<header class="bg-gray-900 text-white flex justify-between items-center px-6 py-4">
    <div class="flex items-center space-x-3">
        <span style="font-size: 32px;">🌊</span>
        <h1 class="text-2xl font-bold">Flood Monitor</h1>
    </div>

    <div class="flex items-center space-x-4">
        @auth
            @if(auth()->user()->is_admin)
                <a href="/admin/dashboard"
                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow">
                    🛠️ Admin Dashboard
                </a>
            @endif

            <!-- Jetstream Dropdown -->
            <x-dropdown align="right" width="48">
                <x-slot name="trigger">
                    <button class="flex items-center bg-gray-700 hover:bg-gray-600 px-3 py-2 rounded">
                        <span>{{ Auth::user()->name }}</span>
                        <svg class="ml-2 w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M5.23 7.21a.75.75 0 011.06.02L10 11.184l3.71-3.954a.75.75 0 111.08 1.04l-4.24 4.52a.75.75 0 01-1.08 0l-4.24-4.52a.75.75 0 01.02-1.06z"
                                clip-rule="evenodd"/>
                        </svg>
                    </button>
                </x-slot>

                <x-slot name="content">

                    <x-dropdown-link href="{{ route('profile.show') }}">
                        {{ __('Profile') }}
                    </x-dropdown-link>

                    <!-- Logout -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-dropdown-link href="{{ route('logout') }}"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                            {{ __('Log Out') }}
                        </x-dropdown-link>
                    </form>

                </x-slot>
            </x-dropdown>
        @else
            <a href="{{ route('login') }}" class="bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded shadow">
                🔑 Login
            </a>
        @endauth
    </div>
</header>
