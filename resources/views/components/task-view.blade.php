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

        {{-- Files (if any) --}}
        @if (!empty($files))
            <div>
                <div class="text-sm font-medium text-gray-800 dark:text-gray-100 mb-2">Uploaded files</div>
                <ul class="list-disc pl-5 space-y-1">
                    @foreach ($files as $f)
                        <li class="text-sm text-gray-800 dark:text-gray-100">
                            <a href="{{ $f['url'] }}" target="_blank" class="text-blue-600 dark:text-blue-400 hover:underline">
                                {{ $f['name'] }}
                            </a>
                            @if(!empty($f['size']))
                                <span class="text-gray-500 dark:text-gray-300">
                                    ({{ number_format($f['size']/1024, 0) }} KB)
                                </span>
                            @endif
                            @if(!empty($f['mime']))
                                <span class="text-gray-400 dark:text-gray-400 text-xs"> — {{ $f['mime'] }}</span>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

    </div>
@endif
