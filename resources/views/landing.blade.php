@extends('layouts.app')

@section('content')
<div class="text-center space-y-6">
    <h1 class="text-4xl font-bold text-indigo-700">Welcome to Fibre Bond Industries</h1>
    <p class="text-gray-600 max-w-xl mx-auto">
        An AI-powered system to evaluate job candidates and streamline recruitment.
    </p>

    <div class="mt-10 flex justify-center gap-6 flex-wrap">
        <!-- Candidate Section -->
        <div class="bg-white rounded-xl shadow-lg p-6 w-80">
            <h2 class="text-xl font-semibold text-indigo-600">For Candidates</h2>
            <p class="text-sm mt-2 text-gray-600">Submit your resume and get an AI-evaluated task.</p>
            <a href="{{ url('/candidate/dashboard') }}"
               class="mt-4 inline-block bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 transition">
                Apply Now
            </a>
        </div>

        <!-- Admin Section -->
        <div class="bg-white rounded-xl shadow-lg p-6 w-80">
            <h2 class="text-xl font-semibold text-gray-800">For Admin</h2>
            <p class="text-sm mt-2 text-gray-600">Manage candidates and view evaluation reports.</p>
            <a href="{{ url('/admin/login') }}"
               class="mt-4 inline-block bg-gray-800 text-white px-6 py-2 rounded-lg hover:bg-gray-900 transition">
                Admin Login
            </a>
        </div>
    </div>
</div>
@endsection
