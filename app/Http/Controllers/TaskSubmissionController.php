<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use App\Models\Candidate;

class TaskSubmissionController extends Controller
{
    public function submit(Request $request)
    {
        $request->validate([
            'candidate_id' => 'required|string',
            'job_role' => 'required|string',
            'task_id' => 'string',
            'code_zip' => 'required|file|mimes:zip',
        ]);

          // ✅ Check if candidate exists
        $candidate = Candidate::where('candidate_id', $request->candidate_id)->first();

        if (!$candidate) {
            return redirect()->back()->withErrors([
                'candidate_id' => 'Invalid Candidate ID. Please check and try again.',
            ])->withInput();
        }
           
        // Step 3: If the candidate has a task_id in DB, validate input task_id
         // ✅ If task_id exists for this candidate in DB, validate it
        if (!empty($candidate->task_id)) {
            $request->validate([
                'task_id' => ['required', function ($attribute, $value, $fail) use ($candidate) {
                    if ($value !== $candidate->task_id) {
                        $fail('Task ID does not match the one assigned to this candidate.');
                    }
                }],
            ]);
        }


        
        // Store file temporarily
        $path = $request->file('code_zip')->store('temp-zips');

        // Call evaluation API
        $response = Http::withOptions(['verify' => false]) // 🔒 Disables SSL verification locally
        ->attach(
            'code_zip',
            Storage::get($path),
            basename($path)
        )
        ->asMultipart()
        ->post('https://aicew.fibrebondindustries.com/evaluate-task', [
            'candidate_id' => $request->candidate_id,
            'job_role'     => $request->job_role,
            'task_id'      => $request->task_id,
        ]);

        // $response = Http::withOptions([
        //     'verify' => app()->environment('local') ? false : true
        // ])
        // ->attach('code_zip', Storage::get($path), basename($path))
        // ->asMultipart()
        // ->post('https://aicew.fibrebondindustries.com/evaluate-task', [
        //     'candidate_id' => $request->candidate_id,
        //     'job_role'     => $request->job_role,
        //     'task_id'      => $request->task_id,
        // ]);

        if ($response->successful()) {
            $data = $response->json();

            // Store in candidates table
            Candidate::where('candidate_id', $data['candidate_id'])
                ->update([
                    'task_score'   => $data['score'],
                    'task_summary' => $data['summary'],
                ]);

            return redirect()->back()->with('success', 'Task evaluated successfully.');
        } else {
            return redirect()->back()->with('error', 'Task evaluation API failed.');
        }
    }
}
