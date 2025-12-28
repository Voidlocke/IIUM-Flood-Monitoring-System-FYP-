@extends('layouts.app')

@section('content')
<div class="min-h-screen
            bg-gradient-to-br from-emerald-100 via-cyan-100 to-sky-200
            py-12">

    <div class="max-w-md mx-auto px-6">

        {{-- Logo --}}
        <div class="flex justify-center mb-4">
            <a href="/">
                <img
                    src="{{ asset('images/flood-monitor-logo.png') }}"
                    alt="Flood Monitor Logo"
                    class="w-32 h-32 drop-shadow-lg"
                >
            </a>
        </div>

        {{-- PAGE TITLE --}}
        <div class="mb-2">
            <div class="flex items-center gap-2">
                <h2 class="text-lg font-bold text-slate-800">
                    Create New Admin
                </h2>
            </div>
        </div>

        {{-- Card --}}
        <div class="bg-white/80 backdrop-blur
                    border border-white/50
                    shadow-2xl rounded-2xl p-6">

            <x-validation-errors class="mb-4" />

            <form method="POST" action="{{ url('/admin/users/store') }}">
                @csrf

                <!-- Name -->
                <div>
                    <label class="block font-semibold text-sm text-slate-700" for="name">
                        Name
                    </label>
                    <input id="name" name="name" type="text"
                           class="block mt-1 w-full rounded-xl border border-slate-300
                                  focus:border-blue-500 focus:ring focus:ring-blue-200/50"
                           required autofocus>
                </div>

                <!-- Email -->
                <div class="mt-4">
                    <label class="block font-semibold text-sm text-slate-700" for="email">
                        Email
                    </label>
                    <input id="email" name="email" type="email"
                           class="block mt-1 w-full rounded-xl border border-slate-300
                                  focus:border-blue-500 focus:ring focus:ring-blue-200/50"
                           required>
                </div>

                <!-- Password -->
                <div class="mt-4">
                    <label class="block font-semibold text-sm text-slate-700" for="password">
                        Password
                    </label>
                    <input id="password" name="password" type="password"
                           class="block mt-1 w-full rounded-xl border border-slate-300
                                  focus:border-blue-500 focus:ring focus:ring-blue-200/50"
                           required>
                </div>

                <!-- Confirm Password -->
                <div class="mt-4">
                    <label class="block font-semibold text-sm text-slate-700" for="password_confirmation">
                        Confirm Password
                    </label>
                    <input id="password_confirmation" name="password_confirmation" type="password"
                           class="block mt-1 w-full rounded-xl border border-slate-300
                                  focus:border-blue-500 focus:ring focus:ring-blue-200/50"
                           required>
                </div>

                <div class="flex items-center justify-end mt-6">
                    <button class="px-6 py-2.5
                                   bg-emerald-600 hover:bg-emerald-700
                                   text-white rounded-xl shadow-lg
                                   font-semibold
                                   transition transform hover:-translate-y-0.5">
                        Create Admin
                    </button>
                </div>
            </form>

        </div>

        {{-- Optional Back button --}}
        <div class="mt-5 flex justify-start">
            <a href="/admin/dashboard"
               class="inline-flex items-center gap-2 px-5 py-2.5
                      bg-blue-600 hover:bg-blue-700
                      text-white rounded-xl shadow
                      font-semibold
                      transition">
                ⬅️ Back to Dashboard
            </a>
        </div>

    </div>
</div>
@endsection
