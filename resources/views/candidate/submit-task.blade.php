@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-2">Submit Your Task</h2>
            <p class="text-sm text-gray-600">Upload your completed task below. Only ZIP format is accepted.</p>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif
            @if($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <ul class="list-disc pl-5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('submit.task') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <div>
                    <label for="candidate_id" class="block text-sm font-medium text-gray-700">Candidate ID</label>
                    <input type="text" name="candidate_id" id="candidate_id" required
                        class="mt-1 block w-full border border-gray-400 rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-500"
                        placeholder="e.g., FBI101" />
                </div>

                <div>
                    <label for="job_id" class="block text-sm font-medium text-gray-700">Job ID</label>
                    <input type="text" name="job_id" id="job_id" required
                        class="mt-1 block w-full border border-gray-400 rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-500"
                        placeholder="e.g., JOB8" />
                </div>

                <div>
                    <label for="job_role" class="block text-sm font-medium text-gray-700">Job Role</label>
                    <input type="text" name="job_role" id="job_role" required
                        class="mt-1 block w-full border border-gray-400 rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-500"
                        placeholder="e.g., Full Stack Developer" />
                </div>

                <div>
                    <label for="code_zip" class="block text-sm font-medium text-gray-700">Upload Task (ZIP)</label>
                    <input type="file" name="code_zip" id="code_zip" required accept=".zip"
                        class="mt-1 block w-full border border-gray-400 rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-500" />
                    <p class="text-xs text-gray-500 mt-1">Only ZIP files are accepted.</p>
                </div>

                <div>
                    <button type="submit"
                        class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:ring-2 focus:ring-offset-1 focus:ring-blue-600 transition">
                        Submit Task
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection


{{-- resources/views/candidate/submit-task.blade.php --}}

{{-- ... your existing HTML ... --}}

<script>
document.addEventListener('DOMContentLoaded', () => {
  const candidateInput = document.getElementById('candidate_id');
  const jobIdInput     = document.getElementById('job_id');
  const jobRoleInput   = document.getElementById('job_role');

  candidateInput.addEventListener('blur', async () => {
    const cid = candidateInput.value.trim();
    if (!cid) return;

    try {
      const res = await fetch(`/lookup/candidate/${encodeURIComponent(cid)}`);
      const data = await res.json();

      if (!data.exists) {
        alert('No application found for this Candidate ID.');
        jobIdInput.value = '';
        // jobRoleInput.value = '';
        return;
      }

      // Auto-fill
      jobIdInput.value = data.job_id || '';
      if (data.job_role) jobRoleInput.value = data.job_role;
    } catch (e) {
      console.error(e);
    }
  });
});
</script>

