@extends('layouts.tailwind')

    @section('content')
    <div class="flex items-center justify-center h-screen bg-gray-100 dark:bg-gray-900">
        <div class="bg-white dark:bg-gray-800 p-8 rounded shadow-md max-w-lg w-full text-center">
            <h1 class="text-2xl font-bold text-red-600 mb-4">Account Pending</h1>
            <p class="text-gray-700 dark:text-gray-300 mb-4">
                Your account is currently inactive or awaiting verification.
            </p>
            <p class="text-gray-600 dark:text-gray-400 mb-6">
                If you're a new user, your account will be reviewed shortly. If you've been deactivated,
                please contact <a href="mailto:admin@storagecheck.global" class="text-blue-600 underline">admin@storagecheck.global</a>.
            </p>
            <!-- <a href="{{ route('filament.admin.auth.login') }}"
               class="inline-block bg-gray-800 text-white px-4 py-2 rounded hover:bg-gray-700 transition">
                Logout
            </a> -->
        </div>
    </div>
    @endsection