@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8 py-10">
    <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-200">
        <div class="px-6 py-6 border-b border-gray-100">
            <h1 class="text-2xl font-semibold tracking-tight text-gray-900">Job Application</h1>
            <p class="mt-1 text-sm text-gray-500">
                Please fill in your details. Fields marked with <span class="text-red-500">*</span> are mandatory.
            </p>
        </div>

        <div class="px-6 py-6">
            @if ($errors->any())
                <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 text-red-700">
                    <div class="font-medium mb-1">Application could not be submitted:</div>
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

            <form action="{{ route('basic-apply.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                @csrf

              {{-- Show the Job Title (read-only) --}}
                @if(!empty($jobId) && !empty($title))
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Applying for</label>
                        <div class="mt-1 flex items-center gap-2">
                            <input type="text" value="{{ $title }}" 
                                class="w-full border rounded px-3 py-2 bg-gray-100" readonly>
                        </div>
                    </div>
                @endif

                @if(request('job_id'))
                    <input type="hidden" name="job_id" value="{{ request('job_id') }}">
                    <!-- <div class="rounded-md bg-blue-50 border border-blue-200 p-2 text-sm text-blue-800">
                        Applying for <strong>{{ request('job_id') }}</strong>
                    </div> -->
                @endif
                @if(!empty($title))
                <input type="hidden" name="job_role" value="{{ $title }}">
                @endif

                {{-- Personal Information --}}
                <section>
                    <h2 class="text-base font-semibold text-gray-900">Personal Information</h2>
                    <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Full Name <span class="text-red-500">*</span></label>
                            <input type="text" name="full_name" value="{{ old('full_name') }}"
                                   class="block w-full rounded-md border border-gray-400 bg-white px-3 py-2 text-sm text-gray-900 placeholder:text-gray-400
                                          focus:border-gray-900 focus:ring-2 focus:ring-gray-900/20" required>
                        </div>
                        <div >
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                            <input type="email" name="email" value="{{ old('email') }}"
                                   class="block w-full rounded-md border border-gray-400 bg-white px-3 py-2 text-sm text-gray-900 placeholder:text-gray-400
                                          focus:border-gray-900 focus:ring-2 focus:ring-gray-900/20" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Mobile <span class="text-red-500">*</span></label>
                            <input type="text" name="mobile" value="{{ old('mobile') }}"
                                   class="block w-full rounded-md border border-gray-400 bg-white px-3 py-2 text-sm text-gray-900 placeholder:text-gray-400
                                          focus:border-gray-900 focus:ring-2 focus:ring-gray-900/20" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Gender</label>
                            <select name="gender"
                                    class="block w-full rounded-md border border-gray-400 bg-white px-3 py-2 text-sm text-gray-900
                                           focus:border-gray-900 focus:ring-2 focus:ring-gray-900/20">
                                <option value="">Select</option>
                                <option value="Male" @selected(old('gender')==='Male')>Male</option>
                                <option value="Female" @selected(old('gender')==='Female')>Female</option>
                                <option value="Other" @selected(old('gender')==='Other')>Other</option>
                                <option value="Prefer not to say" @selected(old('gender')==='Prefer not to say')>Prefer not to say</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                            <input type="text" name="location" value="{{ old('location') }}"
                                   class="block w-full rounded-md border border-gray-400 bg-white px-3 py-2 text-sm text-gray-900 placeholder:text-gray-400
                                          focus:border-gray-900 focus:ring-2 focus:ring-gray-900/20">
                        </div>
                    </div>
                </section>

                {{-- Experience & Compensation --}}
                <section>
                    <h2 class="text-base font-semibold text-gray-900">Experience & Compensation</h2>
                    <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Years of Experience</label>
                            <input type="number" step="0.1" min="0" name="years_of_experience" value="{{ old('years_of_experience') }}"
                                   class="block w-full rounded-md border border-gray-400 bg-white px-3 py-2 text-sm text-gray-900 placeholder:text-gray-400
                                          focus:border-gray-900 focus:ring-2 focus:ring-gray-900/20">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Current Salary</label>
                            <input type="number" step="0.01" min="0" name="current_salary" value="{{ old('current_salary') }}"
                                   class="block w-full rounded-md border border-gray-400 bg-white px-3 py-2 text-sm text-gray-900 placeholder:text-gray-400
                                          focus:border-gray-900 focus:ring-2 focus:ring-gray-900/20">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Expected Salary</label>
                            <input type="number" step="0.01" min="0" name="expected_salary" value="{{ old('expected_salary') }}"
                                   class="block w-full rounded-md border border-gray-400 bg-white px-3 py-2 text-sm text-gray-900 placeholder:text-gray-400
                                          focus:border-gray-900 focus:ring-2 focus:ring-gray-900/20">
                        </div>
                        <div class="md:col-span-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Notice Period</label>
                            <input type="text" name="notice_period" value="{{ old('notice_period') }}" placeholder="e.g., 30 days / Immediate"
                                   class="block w-full rounded-md border border-gray-400 bg-white px-3 py-2 text-sm text-gray-900 placeholder:text-gray-400
                                          focus:border-gray-900 focus:ring-2 focus:ring-gray-900/20">
                        </div>
                    </div>
                </section>

                {{-- Links & Resume --}}
                <section>
                    <h2 class="text-base font-semibold text-gray-900">Links & Resume</h2>
                    <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div >
                            <label class="block text-sm font-medium text-gray-700 mb-1">Link (Website/Portfolio)</label>
                            <input type="url" name="portfolio_link" value="{{ old('portfolio_link') }}" placeholder="https://..."
                                   class="block w-full rounded-md border border-gray-400 bg-white px-3 py-2 text-sm text-gray-900 placeholder:text-gray-400
                                          focus:border-gray-900 focus:ring-2 focus:ring-gray-900/20">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Resume Upload <span class="text-red-500">*</span></label>
                            <input type="file" name="resume" accept=".pdf,.doc,.docx"
                                   class="block w-full text-sm file:mr-4 file:py-2.5 file:px-4 file:rounded-md
                                          file:border-0 file:bg-gray-100 file:text-gray-900 hover:file:bg-gray-200
                                          rounded-md border border-gray-400 bg-white
                                          focus:border-gray-900 focus:ring-2 focus:ring-gray-900/20">
                            <p class="mt-1 text-xs text-gray-500">Allowed: PDF</p>
                        </div>
                    </div>
                </section>

                <div class="pt-2 flex items-center justify-end">
                    <button type="submit"
                            class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                        Submit Application
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
