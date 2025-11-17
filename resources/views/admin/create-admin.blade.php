@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-md mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-md sm:rounded-lg p-6">

            <h2 class="text-2xl font-bold mb-6">Create New Admin</h2>

            <x-validation-errors class="mb-4" />

            <form method="POST" action="{{ url('/admin/users/store') }}">
                @csrf

                <!-- Name -->
                <div>
                    <label class="block font-medium text-sm text-gray-700" for="name">
                        Name
                    </label>
                    <input id="name" name="name" type="text"
                           class="block mt-1 w-full rounded border-gray-300"
                           required autofocus>
                </div>

                <!-- Email -->
                <div class="mt-4">
                    <label class="block font-medium text-sm text-gray-700" for="email">
                        Email
                    </label>
                    <input id="email" name="email" type="email"
                           class="block mt-1 w-full rounded border-gray-300"
                           required>
                </div>

                <!-- Password -->
                <div class="mt-4">
                    <label class="block font-medium text-sm text-gray-700" for="password">
                        Password
                    </label>
                    <input id="password" name="password" type="password"
                           class="block mt-1 w-full rounded border-gray-300"
                           required>
                </div>

                <!-- Confirm Password -->
                <div class="mt-4">
                    <label class="block font-medium text-sm text-gray-700" for="password_confirmation">
                        Confirm Password
                    </label>
                    <input id="password_confirmation" name="password_confirmation" type="password"
                           class="block mt-1 w-full rounded border-gray-300"
                           required>
                </div>

                <div class="flex items-center justify-end mt-6">
                    <button class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-blue-700">
                        Create Admin
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>
@endsection
