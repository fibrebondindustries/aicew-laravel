@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto bg-white shadow-md rounded p-6 mt-10">
    <h2 class="text-2xl font-bold mb-4 text-blue-800">🎉 Application Submitted</h2>

    <p><strong>Candidate ID:</strong> {{ $candidate->candidate_id }}</p>
    <p><strong>Job Role:</strong> {{ $candidate->job_role }}</p>
    <p><strong>Name:</strong> {{ $candidate->name }}</p>
    <p><strong>Email:</strong> {{ $candidate->email }}</p>
    <p><strong>Phone:</strong> {{ $candidate->phone }}</p>
    <p><strong>Experience:</strong> {{ $candidate->experience }} years</p>
    <!-- <p><strong>Resume:</strong> 
        <a href="{{ asset('storage/' . $candidate->resume) }}" target="_blank" class="text-blue-600 underline">
            View Resume
        </a>
    </p> -->

    <hr class="my-4">

       <div class="mt-8 bg-blue-50 border-l-4 border-blue-400 text-blue-700 p-4 rounded">
        <p class="font-semibold">📢 Note:</p>
        <p>   If your profile matches our current requirements, our team will get back to you soon.</p>
    </div>
    <!-- <h3 class="text-xl font-semibold text-green-700 mb-2">AI Evaluation</h3>
    <p><strong>Score:</strong> {{ $candidate->score ?? 'N/A' }}</p>
    <p><strong>Summary:</strong></p>
    <div class="bg-gray-100 p-4 rounded mt-1 text-gray-800">
        {{ $candidate->summary ?? 'AI summary not available.' }}
    </div> -->
</div>
@endsection
