@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto mt-8 bg-white p-6 rounded shadow">
    <h1 class="text-xl font-bold mb-4">🎉 Application Submitted</h1>

    <p><strong>Name:</strong> {{ $candidate->name }}</p>
    <p><strong>Email:</strong> {{ $candidate->email }}</p>
    <p><strong>Phone:</strong> {{ $candidate->phone }}</p>
    <p><strong>Job Role:</strong> {{ $candidate->job_role }}</p>
    <p><strong>Experience:</strong> {{ $candidate->experience }}</p>
    <p><strong>Resume:</strong> <a href="{{ asset('storage/' . $candidate->resume) }}" target="_blank" class="text-blue-600 underline">View Resume</a></p>

    <div class="mt-4">
        <a href="{{ route('home') }}" class="text-blue-500 hover:underline">Back to Home</a>
    </div>
</div>
@endsection
