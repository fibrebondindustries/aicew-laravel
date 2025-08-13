@extends('layouts.app')

@section('content')
@php
    // Build the public apply URL directly in Blade (no controller change)
    $base = app()->environment('local')
        ? 'http://127.0.0.1:8000'
        : rtrim(env('FRONTEND_URL', config('app.url')), '/');

    $applyUrl = $base . '/job-apply?job_id=' . $record->job_id;
@endphp

<div class="flex items-center justify-center min-h-[70vh] px-4">
    <div class="text-center max-w-xl">
        <h1 class="text-4xl md:text-5xl font-extrabold text-gray-900 mb-4">THANK YOU!</h1>

        <div class="flex justify-center mb-6">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-20 w-20 text-green-500" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M16.704 5.29a1 1 0 010 1.414l-7.07 7.071a1 1 0 01-1.415 0L3.296 9.853a1 1 0 111.414-1.414l4.006 4.005 6.364-6.364a1 1 0 011.414 0z" clip-rule="evenodd"/>
            </svg>
        </div>

        {{-- Job ID + copy --}}
        <p class="text-gray-600 mb-2 flex items-center justify-center gap-2">
            Your prompt has been saved. Your Job ID is:
            <span id="jobId" class="font-semibold text-gray-900">{{ $record->job_id }}</span>
            <button onclick="copyText('{{ $record->job_id }}')" class="p-1 text-gray-500 hover:text-gray-700" title="Copy JOB ID">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                     viewBox="0 0 24 24" fill="none" stroke="currentColor"
                     stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                     class="lucide lucide-copy">
                    <rect x="9" y="9" width="13" height="13" rx="2" ry="2"/>
                    <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/>
                </svg>
            </button>
        </p>

        {{-- Apply URL + copy + open --}}
        <div class="mt-5 space-y-2">
            <p class="text-gray-600">Share this link with candidates to apply for this job:</p>

            <div class="flex items-center justify-center gap-3">
                <a href="{{ $applyUrl }}" target="_blank"
                   class="underline text-blue-600 hover:text-blue-700 break-all">
                    {{ $applyUrl }}
                </a>
                <button onclick="copyText(`{{ $applyUrl }}`)" class="p-1 text-gray-500 hover:text-gray-700" title="Copy apply URL">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                         viewBox="0 0 24 24" fill="none" stroke="currentColor"
                         stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                         class="lucide lucide-copy">
                        <rect x="9" y="9" width="13" height="13" rx="2" ry="2"/>
                        <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/>
                    </svg>
                </button>
            </div>

         
        </div>

        <p class="text-gray-500 mt-6">You can safely close this page.</p>
    </div>
</div>

<script>
function copyText(text) {
    // Use Clipboard API when available
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(text)
            .then(() => alert('Copied: ' + text))
            .catch(err => {
                console.error('Copy failed', err);
                fallbackCopy(text);
            });
    } else {
        // Fallback for older browsers / non-secure contexts
        fallbackCopy(text);
    }
}

function fallbackCopy(text) {
    const ta = document.createElement('textarea');
    ta.value = text;
    ta.setAttribute('readonly', '');
    ta.style.position = 'absolute';
    ta.style.left = '-9999px';
    document.body.appendChild(ta);
    ta.select();
    try {
        document.execCommand('copy');
        alert('Copied: ' + text);
    } catch (err) {
        console.error('Fallback copy failed', err);
    }
    document.body.removeChild(ta);
}
</script>
@endsection
