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
            <svg xmlns="http://www.w3.org/2000/svg" style="width: 86px; color: green;" class="h-20 w-20 text-green-500" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M16.704 5.29a1 1 0 010 1.414l-7.07 7.071a1 1 0 01-1.415 0L3.296 9.853a1 1 0 111.414-1.414l4.006 4.005 6.364-6.364a1 1 0 011.414 0z" clip-rule="evenodd"/>
            </svg>
        </div>

        {{-- Main Message --}}
        <p class="text-gray-600 mb-2">
            Your application was submitted successfully. Your Candidate ID is:
            <span class="font-semibold text-gray-900">{{ $app->candidate_id }}</span>
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
@endsection
