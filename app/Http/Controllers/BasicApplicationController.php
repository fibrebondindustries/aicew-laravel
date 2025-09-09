<?php

// app/Http/Controllers/BasicApplicationController.php
namespace App\Http\Controllers;

use App\Models\BasicApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ResumePrompt;
use Illuminate\Validation\Rule;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class BasicApplicationController extends Controller
{
      public function create(Request $request)
    {
          $jobId = $request->query('job_id');   // e.g., JOB6
        $prompt = null;

        if ($jobId) {
            $prompt = ResumePrompt::where('job_id', $jobId)->first();

            // If job doesn't exist or is inactive → 404
            if (! $prompt || ! $prompt->is_active) {
                abort(404, 'This job is no longer available.');
            }
        }

        // return view('candidate.basic-apply'); // Blade below
         // pass $jobId (and optionally $prompt->title) to the form
        return view('candidate.basic-apply', [
            'jobId'  => $jobId,
            'title'  => $prompt?->title,
        ]);
    }

    // public function store(Request $request)
    // {
    //     $validated = $request->validate([
    //         'full_name'           => ['required','string','max:120'],
    //         'email'               => ['required','email','max:150'],
    //         'mobile'              => ['required','string','max:20'],
    //         'gender'              => ['nullable','string','max:20'],
    //         'location'            => ['nullable','string','max:150'],
    //         'years_of_experience' => ['nullable','numeric','min:0','max:99.9'],
    //         'current_salary'      => ['nullable','numeric','min:0'],
    //         'expected_salary'     => ['nullable','numeric','min:0'],
    //         'notice_period'       => ['nullable','string','max:50'],
    //         'portfolio_link'      => ['nullable','url','max:255'],
    //         'resume'              => ['required','file','mimes:pdf,doc,docx','max:5120'], // 5 MB
    //         'job_id'              => ['nullable','string','max:50'],   // <-- accept job_id
    //     ]);

    //     // // Double‑check again (defense in depth)
      
    //     // if (!empty($validated['job_id'])) {
    //     //     $prompt = ResumePrompt::where('job_id', $validated['job_id'])->first();
    //     //     if (! $prompt || ! $prompt->is_active) {
    //     //         abort(404, 'This job is no longer available.');
    //     //     }
          
    //     // }
    //      // NEW: determine the job role (title) from resume_prompts
    //         $jobRole = null;
    //         $promptText = null;   // <-- ensure it always exists
    //         if (!empty($validated['job_id'])) {
    //             $prompt = \App\Models\ResumePrompt::where('job_id', $validated['job_id'])->first();
    //             if (! $prompt || ! $prompt->is_active) {
    //                 abort(404, 'This job is no longer available.');
    //             }
    //             $jobRole = $prompt->title; // e.g. "Full Stack Developer"
    //         }


    //     $app = DB::transaction(function () use ($request, $validated, $jobRole) {
    //         // Generate FBI### safely under lock
    //         $last = BasicApplication::where('candidate_id', 'LIKE', 'FBI%')
    //             ->orderByRaw("CAST(SUBSTRING(candidate_id, 4) AS UNSIGNED) DESC")
    //             ->lockForUpdate()
    //             ->first();

    //         $nextNumber = 101;
    //         if ($last && preg_match('/FBI(\d+)/', $last->candidate_id, $m)) {
    //             $nextNumber = ((int)$m[1]) + 1;
    //         }
    //         $candidateId = 'FBI' . $nextNumber;

    //         // Store resume under candidate-specific folder on public disk
    //         $resumePath = null;
    //         if ($request->hasFile('resume')) {
    //             $resumePath = $request->file('resume')->store("resumes/{$candidateId}", 'public');
    //         }

    //         return BasicApplication::create([
    //             'candidate_id'         => $candidateId,
    //             'full_name'            => $validated['full_name'],
    //             'email'                => $validated['email'],
    //             'mobile'               => $validated['mobile'],
    //             'gender'               => $validated['gender'] ?? null,
    //             'location'             => $validated['location'] ?? null,
    //             'years_of_experience'  => $validated['years_of_experience'] ?? null,
    //             'current_salary'       => $validated['current_salary'] ?? null,
    //             'expected_salary'      => $validated['expected_salary'] ?? null,
    //             'notice_period'        => $validated['notice_period'] ?? null,
    //             'portfolio_link'       => $validated['portfolio_link'] ?? null,
    //             'resume_path'          => $resumePath,
    //             'job_id'               => $validated['job_id'] ?? null, // <-- save if present
    //             'job_role'            => $jobRole, // <-- NEW
    //         ]);
    //     });

    //     // ---- Call Evaluate Resume API (direct URL, multipart/form-data) ----
    //     try {
    //         if ($app->resume_path) {
    //             $absolutePath = Storage::disk('public')->path($app->resume_path);

    //             $response = Http::timeout(60)
    //                 ->acceptJson()
    //                 ->attach('resume', fopen($absolutePath, 'r'), basename($absolutePath))
    //                 ->post('https://aicew.fibrebondindustries.com/evaluate-resume', [
    //                     'candidate_id' => $app->candidate_id,
    //                     'prompt'       => $promptText, // may be null if job_id not provided
    //                 ]);

    //             if ($response->successful()) {
    //                 $data = $response->json();

    //                 // Response body example:
    //                 // { "candidate_id":"FBI106", "score":80, "summary":"..." }
    //                 $app->ai_score   = $data['score']   ?? null;
    //                 $app->ai_summary = $data['summary'] ?? null;
    //                 $app->save();
    //             } else {
    //                 Log::warning('Evaluate Resume API non-200', [
    //                     'status' => $response->status(),
    //                     'body'   => $response->body(),
    //                     'cid'    => $app->candidate_id,
    //                 ]);
    //             }
    //         }
    //     } catch (\Throwable $e) {
    //         Log::error('Evaluate Resume API exception', [
    //             'message' => $e->getMessage(),
    //             'cid'     => $app->candidate_id,
    //         ]);
    //         // Do not block the user; proceed to thank-you page
    //     }
    //     // -------------------------------------------------------------------

    //     return redirect()
    //         ->route('basic-apply.thanks', ['id' => $app->id])
    //         ->with('status', "Application received! Your Candidate ID is {$app->candidate_id}.");
    // }


    public function store(Request $request)
{
     // Normalize email to lowercase to avoid case-sensitive duplicates
    if ($request->filled('email')) {
        $request->merge(['email' => mb_strtolower($request->input('email'))]);
    }
    $validated = $request->validate([
        'full_name'           => ['required','string','max:120'],
       'email'               => [
            'required','email','max:150',
            // Block re-submission by email (global across all jobs)
            Rule::unique('basic_applications', 'email'),
        ],
        'mobile'              => ['required','string','max:20'],
        'gender'              => ['nullable','string','max:20'],
        'location'            => ['nullable','string','max:150'],
        'years_of_experience' => ['nullable','numeric','min:0','max:99.9'],
        'current_salary'      => ['nullable','numeric','min:0'],
        'expected_salary'     => ['nullable','numeric','min:0'],
        'notice_period'       => ['nullable','string','max:50'],
        'portfolio_link'      => ['nullable','url','max:255'],
        'resume'              => ['required','file','mimes:pdf,doc,docx','max:5120'],
        'job_id'              => ['nullable','string','max:50'],
        ], [
        // Custom error message (nice UX)
        'email.unique' => 'An application has already been submitted with this email address.',
    ]);

    // === Get job role & prompt (safe defaults) ===
    $jobRole    = null;
    $promptText = null;  // ensure variable exists always

    if (!empty($validated['job_id'])) {
        $promptRow = ResumePrompt::where('job_id', $validated['job_id'])->first();
        if (!$promptRow || !$promptRow->is_active) {
            abort(404, 'This job is no longer available.');
        }
        $jobRole    = $promptRow->title;           // e.g., "Full Stack Developer"
        $promptText = $promptRow->prompt ?? null;  // <-- REQUIRED: pull prompt text for API
    }

    $app = DB::transaction(function () use ($request, $validated, $jobRole) {
        $last = BasicApplication::where('candidate_id', 'LIKE', 'FBI%')
            ->orderByRaw("CAST(SUBSTRING(candidate_id, 4) AS UNSIGNED) DESC")
            ->lockForUpdate()
            ->first();

        $nextNumber = 101;
        if ($last && preg_match('/FBI(\d+)/', $last->candidate_id, $m)) {
            $nextNumber = ((int)$m[1]) + 1;
        }
        $candidateId = 'FBI' . $nextNumber;

        $resumePath = null;
        if ($request->hasFile('resume')) {
            $resumePath = $request->file('resume')->store("resumes/{$candidateId}", 'public');
        }

        return BasicApplication::create([
            'candidate_id'         => $candidateId,
            'full_name'            => $validated['full_name'],
            'email'                => $validated['email'],
            'mobile'               => $validated['mobile'],
            'gender'               => $validated['gender'] ?? null,
            'location'             => $validated['location'] ?? null,
            'years_of_experience'  => $validated['years_of_experience'] ?? null,
            'current_salary'       => $validated['current_salary'] ?? null,
            'expected_salary'      => $validated['expected_salary'] ?? null,
            'notice_period'        => $validated['notice_period'] ?? null,
            'portfolio_link'       => $validated['portfolio_link'] ?? null,
            'resume_path'          => $resumePath,
            'job_id'               => $validated['job_id'] ?? null,
            'job_role'             => $jobRole,
        ]);
    });

    // === Call Evaluate Resume API (direct URL) ===
    try {
        if ($app->resume_path) {
            $absolutePath = Storage::disk('public')->path($app->resume_path);

            // Build payload conditionally
            $payload = [
                'candidate_id' => $app->candidate_id,
            ];
            if (!is_null($promptText) && $promptText !== '') {
                $payload['prompt'] = $promptText;
            }

            $response = Http::withOptions(['verify' => false]) // ⚠️ dev/test only
                ->timeout(60)
                ->acceptJson()
                ->attach('resume', fopen($absolutePath, 'r'), basename($absolutePath))
                ->post('https://aicew.fibrebondindustries.com/evaluate-resume', $payload);

            if ($response->successful()) {
                $data = $response->json();
                $app->ai_score   = $data['score']   ?? null;
                $app->ai_summary = $data['summary'] ?? null;
                $app->save();
            } else {
                Log::warning('Evaluate Resume API non-200', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                    'cid'    => $app->candidate_id,
                ]);
            }
        }
    } catch (\Throwable $e) {
        Log::error('Evaluate Resume API exception', [
            'message' => $e->getMessage(),
            'cid'     => $app->candidate_id,
        ]);
    }

    return redirect()
        ->route('basic-apply.thanks', ['id' => $app->id])
        ->with('status', "Application received! Your Candidate ID is {$app->candidate_id}.");
}

    public function thankYou($id)
    {
        $app = BasicApplication::findOrFail($id);
        return view('candidate.basic-apply-thanks', compact('app'));
    }
}
