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

                <div>
                    <label class="block text-sm font-medium text-gray-700">Job</label>
                    <select name="job_id" required class="block w-full border-gray-300 rounded-md">
                        <option value="">-- Select Job --</option>
                        @foreach ($jobs as $job)
                            {{-- job_id is a string like JOB7 --}}
                            <option value="{{ $job->job_id }}" @selected(old('job_id') === $job->job_id)>
                                #{{ $job->job_id }} — {{ $job->title }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Task Link (optional)</label>
                    <input type="url" name="task_link"
                        value="{{ old('task_link') }}"
                        placeholder="https://example.com/task-info"
                        class="block w-full rounded-md border-gray-300">
                    @error('task_link') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    <p class="mt-1 text-xs text-gray-500">You may upload files OR provide a link (or both).</p>
                </div>


                <div>
                    <label class="block text-sm font-medium text-gray-700">Task Files (multiple allowed)</label>
                    <input id="task_files" type="file" name="task_files[]" multiple
                           accept=".pdf,.doc,.docx,.csv,.xlsx"
                           class="block w-full border-gray-300 rounded-md">
                    @error('task_files') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    @error('task_files.*') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror

                    <!-- Preview box -->
                    <div id="file_preview"
                         class="mt-3 hidden rounded-xl border border-gray-200 bg-gray-50 p-3">
                        <div class="text-sm font-medium text-gray-700 mb-2">Selected files</div>
                        <ul id="file_list" class="text-sm text-gray-700 space-y-1 list-disc pl-5"></ul>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700">
                        Upload
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Minimal JS for preview --}}
<script>
document.getElementById('task_files').addEventListener('change', function() {
    const box = document.getElementById('file_preview');
    const list = document.getElementById('file_list');
    list.innerHTML = '';
    if (this.files && this.files.length) {
        [...this.files].forEach(f => {
            const li = document.createElement('li');
            li.textContent = `${f.name} (${Math.round(f.size/1024)} KB)`;
            list.appendChild(li);
        });
        box.classList.remove('hidden');
    } else {
        box.classList.add('hidden');
    }
});
</script>
@endsection
