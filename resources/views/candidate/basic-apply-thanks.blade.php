@extends('layouts.app')

@section('content')
<div class="flex items-center justify-center min-h-[70vh] px-4">
    <div class="text-center max-w-xl">

        {{-- Big Thank You Heading --}}
        <h1 class="text-4xl md:text-5xl font-extrabold text-gray-900 mb-4">
            THANK YOU!
        </h1>

        {{-- Green Check Icon --}}
        <div class="flex justify-center mb-6">
            <svg xmlns="http://www.w3.org/2000/svg" style="width: 86px; color: green;"
                class="h-20 w-20 text-green-500" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd"
                    d="M16.704 5.29a1 1 0 010 1.414l-7.07 7.071a1 1 0 01-1.415 0L3.296 9.853a1 1 0 111.414-1.414l4.006 4.005 6.364-6.364a1 1 0 011.414 0z"
                    clip-rule="evenodd"/>
            </svg>
        </div>

        {{-- Main Message --}}
        <p class="text-gray-600 mb-4 flex items-center justify-center gap-2">
            Your application was submitted successfully. Your Candidate ID is:
            <span id="candidateId" class="font-semibold text-gray-900">{{ $app->candidate_id }}</span>

            {{-- Copy Button --}}
            <button onclick="copyCandidateId()" class="p-1 text-gray-500 hover:text-gray-700" title="Copy Candidate ID">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="lucide lucide-copy">
                    <rect x="9" y="9" width="13" height="13" rx="2" ry="2"/>
                    <path d="M5 15H4a2 2 0 0 1-2-2V4
                             a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/>
                </svg>
            </button>
        </p>

        {{-- Link --}}
        @if($app->resume_path)
            <p class="mb-6">
                <a href="{{ Storage::url($app->resume_path) }}"
                   class="text-green-600 hover:text-green-700 underline text-lg">
                    Download your uploaded resume here
                </a>
            </p>
        @endif

        {{-- Closing Note --}}
        <p class="text-gray-500">
            We’ll review your application and get back to you shortly!
        </p>
    </div>
</div>

{{-- Script for Copy --}}
<script>
    function copyCandidateId() {
        const text = document.getElementById('candidateId').innerText;
        navigator.clipboard.writeText(text).then(() => {
            alert('Candidate ID copied to clipboard!');
        }).catch(err => {
            console.error('Failed to copy text: ', err);
        });
    }
</script>
@endsection
