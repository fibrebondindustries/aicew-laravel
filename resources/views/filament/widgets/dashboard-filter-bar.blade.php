@php
    // Base URL: local vs prod
    $base = app()->environment('local')
        ? 'http://127.0.0.1:8000'
        : rtrim(env('FRONTEND_URL', config('app.url')), '/');

    // Static links
    $taskUploadUrl = $base . '/tasks/upload';
    $jobPromptUrl  = $base . '/resume/prompt';

   
@endphp


<div class="mb-4">
    <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">

        {{-- Job Role --}}
        <div>
            <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-200">
                Job Role
            </label>
            <select
                wire:model.live="filter_job_role"
                class="fi-input block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm
                       text-gray-900 placeholder-gray-500 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500
                       dark:border-white/10 dark:bg-white/5 dark:text-white dark:placeholder-white/50">
                <option value="">All</option>
                @foreach($this->getJobRoleOptions() as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>

        {{-- Job ID --}}
        <div>
            <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-200">
                Job ID
            </label>
            <input
                type="text"
                placeholder="e.g. JOB101"
                wire:model.live.debounce.500ms="filter_job_id"
                class="fi-input block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm
                       text-gray-900 placeholder-gray-500 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500
                       dark:border-white/10 dark:bg-white/5 dark:text-white dark:placeholder-white/50" />
        </div>

        {{-- Job Posted (Applied On) --}}
        <div>
            <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-200">
                Job Posted (Applied On)
            </label>
            <div class="flex gap-2">
                <input
                    type="date"
                    wire:model.live="filter_posted.from"
                    class="fi-input block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm
                           text-gray-900 placeholder-gray-500 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500
                           dark:border-white/10 dark:bg-white/5 dark:text-white dark:placeholder-white/50" />
                <input
                    type="date"
                    wire:model.live="filter_posted.to"
                    class="fi-input block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm
                           text-gray-900 placeholder-gray-500 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500
                           dark:border-white/10 dark:bg-white/5 dark:text-white dark:placeholder-white/50" />
            </div>
        </div>

    </div>

    {{-- Quick actions row --}}
    <div class="mt-3 flex flex-wrap gap-2">
        {{-- Apply to Job (enabled only when Job ID is set) --}}
      

        {{-- Task Upload --}}
        <a href="{{ $taskUploadUrl }}" target="_blank" rel="noopener"
           class="inline-flex items-center rounded-lg bg-emerald-600 px-3 py-2 text-sm font-medium
                  hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500">
            Task Upload
        </a>

        {{-- Job Prompt Post --}}   
        <a href="{{ $jobPromptUrl }}" target="_blank" rel="noopener"
           class="inline-flex items-center rounded-lg bg-indigo-600 px-3 py-2 text-sm font-medium
                  hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            Resume  Prompt
        </a>
    </div>
</div>