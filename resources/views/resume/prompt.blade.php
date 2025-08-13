@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8 py-10">
    <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-200 overflow-hidden">
        <div class="px-6 py-6 border-b border-gray-100">
            <h1 class="text-2xl font-semibold tracking-tight text-gray-900">Resume Prompt</h1>
            <p class="mt-1 text-sm text-gray-500">Enter your prompt below and submit.</p>
        </div>

        <div class="px-6 py-6">
            @if ($errors->any())
                <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 text-red-700">
                    <div class="font-medium mb-2">Please fix the following:</div>
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach ($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('status'))
                <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-800">
                    {{ session('status') }}
                </div>
            @endif

            <form action="{{ route('resume.prompt.store') }}" method="POST" class="space-y-6">
                @csrf

              <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Job Role <span class="text-red-500">*</span></label>
                    <input type="text" name="title" value="{{ old('title') }}"
                        class="block w-full rounded-md border border-gray-400 bg-white px-3 py-2 text-sm text-gray-900
                                placeholder:text-gray-400 focus:border-gray-900 focus:ring-2 focus:ring-gray-900/20" required>
                    @error('title') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Prompt <span class="text-red-500">*</span></label>
                    <textarea name="prompt" rows="8"
                              class="block w-full rounded-md border border-gray-400 bg-white px-3 py-2 text-sm text-gray-900
                                     placeholder:text-gray-400 focus:border-gray-900 focus:ring-2 focus:ring-gray-900/20"
                              placeholder="Type your resume prompt here...">{{ old('prompt') }}</textarea>
                </div>

                <div class="pt-2 flex items-center justify-end">
                    <button type="submit"
                            class="inline-flex items-center justify-center rounded-md bg-blue-600 px-6 py-2
                                   text-sm font-medium text-white shadow-sm hover:bg-blue-700
                                   focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
                        Save Prompt
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
