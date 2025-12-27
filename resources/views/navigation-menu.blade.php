<nav x-data="{ open:false, mobile:false }" class="px-6 py-3 shadow-md" style="background:#1f2937;color:#fff;">
    <div class="flex justify-between items-center">

        {{-- Left: Logo --}}
        <div class="flex items-center gap-3">
            <a href="{{ url('/') }}" class="flex items-center gap-4">
                <!-- Logo wrapper -->
                <div class="h-12 w-12 flex items-center justify-center">
                    <img
                        src="{{ asset('images/flood-monitor-logo.png') }}"
                        alt="Flood Monitor Logo"
                        class="h-full w-auto scale-150 drop-shadow-lg"
                    >
                </div>

                <!-- Title -->
                <span class="text-xl md:text-2xl font-extrabold tracking-wide">
                    IIUM Flood Monitor
                </span>
            </a>

        </div>

        {{-- Right --}}
        <div class="hidden sm:flex items-center space-x-4">

            {{-- Admin Buttons --}}
            @if(Auth::check() && Auth::user()->is_admin)

                {{-- If on admin dashboard → show Back to Home --}}
                @if(request()->is('admin*'))
                    <a href="{{ url('/') }}"
                       class="btn-login bg-green-500 px-4 py-2 rounded text-white font-medium hover:bg-green-600">
                       ⬅️ Back to Home
                    </a>

                {{-- If not on admin dashboard → show Admin Dashboard --}}
                @else
                    <a href="{{ url('/admin/dashboard') }}"
                       class="bg-blue-500 px-4 py-2 rounded text-white font-medium hover:bg-blue-600">
                       🛠️ Admin Dashboard
                    </a>
                @endif

            @endif

            {{-- User Menu --}}
            @if(Auth::check())
                <div x-data="{ open:false }" class="relative">

                    <button @click="open = !open"
                        class="flex items-center gap-1 px-3 py-2 rounded hover:bg-[#374151]">
                        {{ Auth::user()->name }}
                        <svg width="16" height="16" fill="white" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M5.23 7.21a.75.75 0 011.06.02L10 11l3.71-3.77a.75.75 0 111.08 1.04l-4.24 4.3a.75.75 0 01-1.08 0L5.21 8.27a.75.75 0 01.02-1.06z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>

                    <div x-show="open" @click.outside="open=false" x-cloak
                        class="absolute right-0 mt-2 w-40 bg-white text-gray-800 rounded shadow-lg py-2 z-50">

                        <a href="{{ route('profile.show') }}" class="block px-4 py-2 hover:bg-gray-100">
                            Profile
                        </a>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-2 hover:bg-gray-100">
                                Log Out
                            </button>
                        </form>
                    </div>
                </div>
            @endif

        </div>

        {{-- Mobile Menu Button --}}
        <div class="sm:hidden">
            <button @click="mobile = !mobile">
                <svg class="w-7 h-7" fill="white" viewBox="0 0 24 24">
                    <path d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
        </div>
    </div>

    {{-- Mobile Dropdown --}}
    <div x-show="mobile" x-cloak class="sm:hidden mt-3 bg-[#111827] p-3 rounded">

        @if(Auth::check() && Auth::user()->is_admin)
            @if(request()->is('admin*'))
                <a href="{{ url('/') }}"
                   class="block w-full px-3 py-2 mb-2 bg-blue-500 rounded text-center">
                   ⬅️ Back to Home
                </a>
            @else
                <a href="{{ url('/admin/dashboard') }}"
                   class="block w-full px-3 py-2 mb-2 bg-blue-500 rounded text-center">
                   🛠️ Admin Dashboard
                </a>
            @endif
        @endif

        <a href="{{ route('profile.show') }}" class="block py-2">Profile</a>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="block py-2 text-left">Log Out</button>
        </form>

    </div>
</nav>
