<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Candidate;
use App\Models\Job;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Log;

class CandidateController extends Controller
{
    // public function store(Request $request, $slug = null)
    // {
    //     // Validate form input
    //     $request->validate([
    //         'name'      => 'required|string|max:255',
    //         'email'     => 'required|email',
    //         'phone'     => 'required',
    //         'resume'    => 'required|mimes:pdf,doc,docx|max:2048',
    //         'experience' => 'required',
    //     ]);

    //     // Fetch the related job (by slug or form field)
    //     $job = $slug 
    //         ? Job::where('slug', $slug)->firstOrFail()
    //         : Job::findOrFail($request->job_id);

    //     // Handle file upload
    //     if ($request->hasFile('resume')) {
    //         $resumePath = $request->file('resume')->store('resumes', 'public');
    //     } else {
    //         return back()->withErrors(['resume' => 'Resume upload failed.']);
    //     }

    //     // ✅ Generate unique candidate_id like FBI101
    //     $lastCandidate = Candidate::orderBy('candidate_id', 'desc')->first();
    //     if ($lastCandidate && preg_match('/FBI(\d+)/', $lastCandidate->candidate_id, $matches)) {
    //         $newIdNumber = (int)$matches[1] + 1;
    //     } else {
    //         $newIdNumber = 101; // Start from FBI101
    //     }
    //     $candidateId = 'FBI' . $newIdNumber;

    //       // ✅ Prepare data for external API
    //     $apiUrl = 'https://aicew.fibrebondindustries.com/evaluate-resume';

    //      $apiResponse = Http::withOptions([
    //         'verify' => false, // ⛔ DO NOT USE IN PRODUCTION
    //     ])
    //     ->attach(
    //         'resume',
    //         file_get_contents(storage_path('app/public/' . $resumePath)),
    //         $request->file('resume')->getClientOriginalName()
    //     )
    //     ->post($apiUrl, [
    //         'candidate_id' => $candidateId,
    //         'job_role'     => $job->title,
    //         'jd'           => $job->description,
    //         'experience'   => $request->experience,
    //     ]);

    //     $score   = null;
    //     $summary = null;

    //     if ($apiResponse->successful()) {
    //         $score   = $apiResponse['score'] ?? null;
    //         $summary = $apiResponse['summary'] ?? null;
    //     }

        
    //     // Create the candidate record
    //     Candidate::create([
    //         'candidate_id' => $candidateId,
    //         'job_id'       => $job->id,
    //         'job_role'     => $job->title,
    //         'name'         => $request->name,
    //         'email'        => $request->email,
    //         'phone'        => $request->phone,
    //         'resume'       => $resumePath,
    //         'experience'   => $request->experience ?? 'N/A',
    //         'score'        => null,
    //     ]);

    //     // Redirect to candidate dashboard
    //     return redirect()->route('candidate.dashboard')->with('success', 'Application submitted successfully!');
    // }

    public function store(Request $request, $slug = null)
        {
            $request->validate([
                'name'      => 'required|string|max:255',
                'email'     => 'required|email',
                'phone'     => 'required',
                'resume'    => 'required|mimes:pdf,doc,docx|max:2048',
                'experience'=> 'required',
            ]);

            $job = $slug 
                ? Job::where('slug', $slug)->firstOrFail()
                : Job::findOrFail($request->job_id);

            // if ($request->hasFile('resume')) {
            //     $resumePath = $request->file('resume')->store('resumes', 'public');
            // } else {
            //     return back()->withErrors(['resume' => 'Resume upload failed.']);
            // }

            $lastCandidate = Candidate::orderBy('candidate_id', 'desc')->first();
            $newIdNumber = ($lastCandidate && preg_match('/FBI(\d+)/', $lastCandidate->candidate_id, $matches)) 
                ? ((int)$matches[1] + 1) 
                : 101;

            $candidateId = 'FBI' . $newIdNumber;


            // ✅ Clean name and set custom filename
            $cleanName = preg_replace('/[^a-zA-Z0-9]/', '', $request->name); // Remove spaces/special chars
            $extension = $request->file('resume')->getClientOriginalExtension();
            $fileName = "{$candidateId}_{$cleanName}.{$extension}";

            // ✅ Store resume
            $resumePath = $request->file('resume')->storeAs('resumes', $fileName, 'public');

            // ✅ Create candidate FIRST
            $candidate = Candidate::create([
                'candidate_id' => $candidateId,
                'job_id'       => $job->id,
                'job_role'     => $job->title,
                'name'         => $request->name,
                'email'        => $request->email,
                'phone'        => $request->phone,
                'resume'       => $resumePath,
                'experience'   => $request->experience,
                'score'        => null,
            ]);

            // ✅ Call evaluation API
            $apiResponse = Http::withOptions(['verify' => false]) // ⚠️ Remove 'verify' in production
                ->attach(
                    'resume',
                    file_get_contents(storage_path('app/public/' . $resumePath)),
                    $request->file('resume')->getClientOriginalName()
                )->post('https://aicew.fibrebondindustries.com/evaluate-resume', [
                    'candidate_id' => $candidateId,
                    'job_role'     => $job->title,
                    'jd'           => $job->description,
                    'experience'   => $request->experience,
                ]);

            if ($apiResponse->successful()) {
                $data = $apiResponse->json();
                $candidate->score = $data['score'] ?? null;
                $candidate->summary = $data['summary'] ?? null;
                // $candidate->save(); // 💾 Update record
            }
            // ✅ Only fetch task if not Data Analyst
                // Step 6: If NOT a Data Analyst, fetch task
        // if (strtolower($job->title) !== 'data analyst') {
        //     $apiJobRoles = [
        //         'laravel developer'     => 'laravel_developer',
        //         'react developer'       => 'react_developer',
        //         'full stack developer'  => 'full_stack_developer',
        //     ];

        //     $jobTitleKey = strtolower(trim($job->title));
        //     $mappedJobRole = $apiJobRoles[$jobTitleKey] ?? null;

        //     if ($mappedJobRole) {
        //         $taskResponse = Http::withOptions(['verify' => false])
        //             ->post('https://aicew.fibrebondindustries.com/get-developer-task', [
        //                 'experience' => $request->experience,
        //                 'job_role'   => $mappedJobRole,
        //             ]);

        //         if ($taskResponse->successful()) {
        //             $taskData = $taskResponse->json();
        //             logger($taskData); // For debugging only, remove in prod

        //             $candidate->task_title       = $taskData['title'] ?? null;
        //             $candidate->task_description = $taskData['description'] ?? null;
        //             $candidate->expected_output  = $taskData['expected_output'] ?? null;
        //             $candidate->task_id          = $taskData['task_id'] ?? null;

        //               // ✅ Send task email
        //         $sendTaskMailResponse = Http::withOptions(['verify' => false])
        //             ->post('https://aicew.fibrebondindustries.com/send-task-mail', [
        //                 'job_role'     => $job->title,
        //                 'candidate_id' => $candidate->candidate_id,
        //                 'email'        => $candidate->email,
        //                 'task'         => $taskData,
        //             ]);

        //         if (!$sendTaskMailResponse->successful()) {
        //             logger(['error' => 'Send task mail API failed', 'response' => $sendTaskMailResponse->body()]);
        //         }
        //         } else {
        //             logger(['error' => 'Task API failed', 'response' => $taskResponse->body()]);
        //         }
        //         } else {
        //             logger(['error' => 'Job role not found', 'title' => $job->title]);
        //         }
                
        //         }
        $taskData = null;

            $apiJobRoles = [
                'laravel developer'     => 'laravel_developer',
                'react developer'       => 'react_developer',
                'full stack developer'  => 'full_stack_developer',
            ];

            $jobTitleKey = strtolower(trim($job->title));
            $mappedJobRole = $apiJobRoles[$jobTitleKey] ?? null;

            // ✅ If mapped, fetch task from API
            if ($mappedJobRole) {
                $taskResponse = Http::withOptions(['verify' => false])
                    ->post('https://aicew.fibrebondindustries.com/get-developer-task', [
                        'experience' => $request->experience,
                        'job_role'   => $mappedJobRole,
                    ]); 

                if ($taskResponse->successful()) {
                    $taskData = $taskResponse->json();
                    $candidate->task_title       = $taskData['title'] ?? null;
                    $candidate->task_description = $taskData['description'] ?? null;
                    $candidate->expected_output  = $taskData['expected_output'] ?? null;
                    $candidate->task_id          = $taskData['task_id'] ?? null;
                } else {
                    logger(['error' => 'Task API failed', 'response' => $taskResponse->body()]);
                }
            }

            // ✅ If no taskData (e.g., Data Analyst), fallback to empty task
            if (!$taskData) {
                $taskData = [
                    'title' => 'No task assigned',
                    'description' => 'No developer task applicable for this role.',
                    'expected_output' => [],
                    'task_id' => null,
                    'input_files' => [],
                ];
            }

            // // ✅ Always send task mail
            // $sendTaskMailResponse = Http::withOptions(['verify' => false])
            //     ->post('https://aicew.fibrebondindustries.com/send-task-mail', [
            //         'job_role'     => $job->title,
            //         'candidate_id' => $candidate->candidate_id,
            //         'email'        => $candidate->email,
            //         'task'         => $taskData,
            //     ]);

            // if (!$sendTaskMailResponse->successful()) {
            //     logger(['error' => 'Send task mail API failed', 'response' => $sendTaskMailResponse->body()]);
            // }
            // ✅ Always send task mail and log the request + response
            try {
                Log::info('Triggering send-task-mail API', [
                    'job_role'     => $job->title,
                    'candidate_id' => $candidate->candidate_id,
                    'email'        => $candidate->email,
                    'task'         => $taskData,
                ]);

                $sendTaskMailResponse = Http::withOptions(['verify' => false])
                    ->post('https://aicew.fibrebondindustries.com/send-task-mail', [
                        'job_role'     => $job->title,
                        'candidate_id' => $candidate->candidate_id,
                        'email'        => $candidate->email,
                        'task'         => $taskData,
                    ]);

                if ($sendTaskMailResponse->successful()) {
                    Log::info('✅ Email API succeeded', [
                        'response' => $sendTaskMailResponse->json()
                    ]);
                } else {
                    Log::error('❌ Email API failed', [
                        'status'   => $sendTaskMailResponse->status(),
                        'response' => $sendTaskMailResponse->body(),
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('❗ Exception while calling email API', [
                    'message' => $e->getMessage()
                ]);
            }

                // 💾 Save candidate
                $candidate->save();

                return redirect()->route('candidate.dashboard')->with('success', 'Application submitted successfully!');
            }

    public function dashboard()
        {
            // Fetch the most recent candidate (or use auth logic if needed)
            $candidate = Candidate::latest()->first();

            if (!$candidate) {
                return view('candidate.dashboard')->with('error', 'No application found.');
            }

            return view('candidate.dashboard', compact('candidate'));
        }

    public function downloadResume($id)
        {
            $candidate = Candidate::findOrFail($id);

            if (!$candidate->resume || !Storage::disk('public')->exists($candidate->resume)) {
                abort(404, 'Resume file not found.');
            }

            $resumePath = Storage::disk('public')->path($candidate->resume);
            $filename = $candidate->candidate_id . '_' . $candidate->name . '_resume.pdf';

            return response()->download($resumePath, $filename);
        }

}
