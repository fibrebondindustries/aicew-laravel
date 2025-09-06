<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use App\Models\Candidate;
use App\Models\ResumePrompt;
use App\Models\BasicApplication;

class TaskSubmissionController extends Controller
{
    public function submit(Request $request)
    {
        $request->validate([
            'candidate_id' => 'required|string',
            // job_id may be empty if you want to auto-fill; we’ll try to infer it
            'job_id'       => 'nullable|string',
            'job_role'     => 'required|string',
            'code_zip'     => 'required|file|mimes:zip',
        ]);

        // Candidate must exist
        $candidate = BasicApplication::where('candidate_id', $request->candidate_id)->first();
        if (!$candidate) {
            return back()->withErrors([
                'candidate_id' => 'Invalid Candidate ID. Please check and try again.',
            ])->withInput();
        }

        // If job_id not provided, try to fetch from basic_applications
        $jobId = $request->job_id;
        if (blank($jobId)) {
            $existingApp = BasicApplication::where('candidate_id', $request->candidate_id)
                ->latest('id')->first();
            $jobId = $existingApp?->job_id;
        }

        if (blank($jobId)) {
            return back()->withErrors([
                'job_id' => 'Unable to determine Job ID for this candidate.',
            ])->withInput();
        }

        // Look up task mode & prompt
        $rp = ResumePrompt::where('job_id', $jobId)->first();
        $taskMode    = $rp?->task_mode ?? 'manual';
        $taskPrompt  = $rp?->task_prompt;

        // Store the uploaded ZIP
        $zipPath = $request->file('code_zip')->store('candidate-tasks', 'public');

        // Prepare data to persist
        $update = ['code_zip' => $zipPath];

        // AI evaluate → call API and store results
        if ($taskMode === 'ai') {
            $response = Http::withOptions(['verify' => false])
                ->attach('code_zip', Storage::disk('public')->get($zipPath), basename($zipPath))
                ->asMultipart()
                ->post('https://aicew.fibrebondindustries.com/evaluate-task', [
                    'candidate_id' => $request->candidate_id,
                    'prompt'       => $taskPrompt, // critical: send stored prompt
                ]);

            if (!$response->successful()) {
                return back()->with('error', 'AI Task evaluation failed.');
            }

            $json = $response->json();
            $update['task_score']   = $json['score']   ?? null;
            $update['task_summary'] = $json['summary'] ?? null;
        }

        // Save against the candidate & job
        BasicApplication::updateOrCreate(
            ['candidate_id' => $request->candidate_id, 'job_id' => $jobId],
            $update
        );

        return back()->with('success', 'Task submitted successfully.');
    }

    // ---- JSON endpoint to auto-fill job by candidate ----
    public function lookupByCandidate(string $candidateId)
    {
        $app = BasicApplication::where('candidate_id', $candidateId)
            ->latest('id')->first();

        if (!$app) {
            return response()->json(['exists' => false]);
        }

        return response()->json([
            'exists'   => true,
            'job_id'   => $app->job_id,
            'job_role' => $app->job_role,
        ]);
    }
}