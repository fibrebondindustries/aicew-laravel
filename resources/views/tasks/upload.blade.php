@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8 py-10">
    <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-200 overflow-hidden">
        <div class="px-6 py-6 border-b border-gray-100">
            <h1 class="text-2xl font-semibold tracking-tight text-gray-900">Upload Task File</h1>
            <p class="mt-1 text-sm text-gray-500">Select a Job and upload your task files.</p>
        </div>

        <div class="px-6 py-6">
            @if ($errors->any())
                <div class="mb-6 border border-red-200 bg-red-50 p-4 text-red-700 rounded-xl">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('status'))
                <div class="mb-6 border border-emerald-200 bg-emerald-50 p-4 text-emerald-800 rounded-xl">
                    {{ session('status') }}
                </div>
            @endif

            <form action="{{ route('tasks.upload.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                {{-- Job --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Job</label>
                    <select name="job_id" required
                        class="block w-full rounded-md border border-gray-400 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">-- Select Job --</option>
                        @foreach ($jobs as $job)
                            <option value="{{ $job->job_id }}" @selected(old('job_id') === $job->job_id)>
                                #{{ $job->job_id }} — {{ $job->title }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Task Mode --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Task Mode</label>
                    <select name="task_mode" id="task_mode" required
                        class="block w-full rounded-md border border-gray-400 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="ai" @selected(old('task_mode','ai') === 'ai')>AI evaluate</option>
                        <option value="manual" @selected(old('task_mode') === 'manual')>Manual evaluate</option>
                    </select>
                </div>

                {{-- Prompt (optional) --}}
                <div id="prompt_wrap" class="@if(old('task_mode','ai') !== 'ai') hidden @endif">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Prompt (optional)</label>
                    <textarea name="task_prompt" rows="6"
                        class="block w-full rounded-md border border-gray-400 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Enter the AI evaluation task_prompt...">{{ old('task_prompt') }}</textarea>
                </div>

                {{-- Task Link --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Task Link</label>
                    <input type="url" name="task_link"
                        value="{{ old('task_link') }}"
                        placeholder="https://example.com/task-info"
                        class="block w-full rounded-md border border-gray-400 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @error('task_link') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Submit --}}
                <div class="flex justify-end">
                    <button type="submit"
                        class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 focus:ring-2 focus:ring-offset-1 focus:ring-blue-600">
                        Upload
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
const modeEl = document.getElementById('task_mode');
const promptWrap = document.getElementById('prompt_wrap');

function togglePrompt() {
    if (modeEl.value === 'ai') {
        promptWrap.classList.remove('hidden');
    } else {
        promptWrap.classList.add('hidden');
    }
}
modeEl?.addEventListener('change', togglePrompt);
togglePrompt();
</script>
@endsection
