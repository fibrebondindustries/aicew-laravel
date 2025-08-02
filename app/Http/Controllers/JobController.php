<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\Candidate;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class JobController extends Controller
{
    public function index()
    {
        $jobs = Job::active()->latest()->get();
        return view('jobs.index', compact('jobs'));
    }

    // public function show($slug)
    // {
    //     $job = Job::where('slug', $slug)->active()->firstOrFail();

    //     // Generate hidden candidate_id
    //     $candidateId = 'CAND-' . strtoupper(Str::random(8));

    //     return view('jobs.show', compact('job', 'candidateId'));
    // }

    public function showByIndeedId($indeedJobId)
    {
        $job = Job::where('indeed_job_id', $indeedJobId)->active()->firstOrFail();
        return view('jobs.show', compact('job'));
    }

    public function showByRole($role)
    {
        $jobTitle = str_replace('-', ' ', $role);
        $jobTitle = ucwords($jobTitle);

        $job = Job::where('title', 'LIKE', "%{$jobTitle}%")
            ->orWhere('slug', 'LIKE', "%{$role}%")
            ->active()
            ->firstOrFail();

        return view('jobs.show', compact('job'));
    }

   public function apply(Request $request, $slug)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email',
        'phone' => 'required|string',
        'resume' => 'required|mimes:pdf,doc,docx|max:2048',
    ]);

    $job = Job::where('slug', $slug)->firstOrFail();

    // ✅ Save resume to disk
    $resumePath = $request->file('resume')->store('resumes', 'public');

    // ✅ Create candidate record
    $candidate = Candidate::create([
        'name' => $request->name,
        'email' => $request->email,
        'phone' => $request->phone,
        'resume' => $resumePath,
        'job_id' => $job->id,
        'job_role' => $job->title,
        'experience' => $job->experience_level, // Or replace with input if you're taking from form
    ]);

    // ✅ Prepare API request to FastAPI backend
    $apiUrl = 'https://aicew.fibrebondindustries.com/evaluate-resume';

    try {
        $response = Http::attach(
            'resume', file_get_contents(storage_path("app/public/{$resumePath}")), basename($resumePath)
        )->post($apiUrl, [
            'candidate_id' => $candidate->id,
            'job_role'     => $candidate->job_role,
            'jd'           => $job->requirements, // or any JD string
            'experience'   => $candidate->experience,
        ]);

        if ($response->successful()) {
            $data = $response->json();
            $candidate->score = $data['score'] ?? null;
            $candidate->summary = $data['summary'] ?? null;
            $candidate->save();
        } else {
            \Log::error('Resume API failed', ['response' => $response->body()]);
        }
    } catch (\Exception $e) {
        \Log::error('Resume evaluation error', ['error' => $e->getMessage()]);
    }

    return redirect()->back()->with('success', 'Your application has been submitted successfully!');
}
    
    private function evaluateCandidate($candidate, $job)
    {
        try {
            $aicewApiUrl = 'https://aicew.fibrebondindustries.com';

            $resumeData = [
                'resume_content' => $this->extractTextFromResume($candidate->resume),
                'job_description' => $job->description,
                'job_requirements' => $job->requirements,
            ];

            $response = \Http::post($aicewApiUrl . '/evaluate-resume', $resumeData);

            if ($response->successful()) {
                $evaluation = $response->json();
                $candidate->update([
                    'score' => $evaluation['score'] ?? null,
                    'summary' => $evaluation['summary'] ?? null,
                    'evaluation_data' => json_encode($evaluation),
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('AICEW API Error: ' . $e->getMessage());
        }
    }

    private function extractTextFromResume($resumePath)
    {
        $filePath = storage_path('app/public/' . $resumePath);
        return file_exists($filePath) ? file_get_contents($filePath) : '';
    }

    public function apiGetJobByRole($role)
    {
        $job = Job::where('title', 'LIKE', "%{$role}%")
            ->orWhere('slug', 'LIKE', "%{$role}%")
            ->active()
            ->first();

        if (!$job) {
            return response()->json(['error' => 'Job not found'], 404);
        }

        return response()->json([
            'job' => [
                'id' => $job->id,
                'title' => $job->title,
                'description' => $job->description,
                'requirements' => $job->requirements,
                'responsibilities' => $job->responsibilities,
                'location' => $job->location,
                'type' => $job->type,
                'experience_level' => $job->experience_level,
                'salary' => $job->formatted_salary,
                'apply_url' => route('jobs.show', $job->slug),
            ]
        ]);
    }
}
