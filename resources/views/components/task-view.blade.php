@php
    /** @var array $files */
    $link = $link ?? null;
@endphp

@if (empty($files) && empty($link))
    <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 p-4 text-sm text-gray-500 dark:text-gray-300">
        No files or link provided.
    </div>
@else
    <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 p-4 space-y-4">

        {{-- Task link (if any) --}}
        @if (filled($link))
            <div>
                <div class="text-sm font-medium text-gray-800 dark:text-gray-100 mb-1">Task Link</div>
                <a href="{{ $link }}" target="_blank" class="text-blue-600 dark:text-blue-400 hover:underline break-all">
                    {{ $link }}
                </a>
            </div>
        @endif

      

    </div>
@endif
