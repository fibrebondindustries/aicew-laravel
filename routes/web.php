<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CandidateController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\TaskSubmissionController;
use App\Http\Controllers\BasicApplicationController;
use App\Http\Controllers\ResumePromptController;
use App\Http\Controllers\TaskUploadController;


/*
|--------------------------------------------------------------------------
| Landing / Static
|--------------------------------------------------------------------------
*/
Route::view('/', 'landing');
Route::view('/apply', 'apply');

/*
|--------------------------------------------------------------------------
| Jobs (Public)
|--------------------------------------------------------------------------
*/
// Jobs listing
Route::get('/jobs', [JobController::class, 'index'])->name('jobs.index');

// Canonical job detail page (use this everywhere)
Route::get('/career/{slug}', [JobController::class, 'show'])
    ->where('slug', '[A-Za-z0-9\-]+')
    ->name('jobs.show');

    
// Role-based lookup (separate path to avoid conflict with {slug})
Route::get('/career/role/{role}', [JobController::class, 'showByRole'])
    ->where('role', '[A-Za-z0-9\-\s]+')
    ->name('jobs.by-role');

// Indeed job id
Route::get('/indeed/{indeedJobId}', [JobController::class, 'showByIndeedId'])
    ->where('indeedJobId', '[A-Za-z0-9\-]+')
    ->name('jobs.by-indeed-id');

/*
|--------------------------------------------------------------------------
| Legacy redirects (SEO-safe)
|--------------------------------------------------------------------------
*/
// Old /jobs/{slug} → canonical /career/{slug}
Route::get('/jobs/{slug}', function (string $slug) {
    return redirect()->route('jobs.show', ['slug' => $slug], 301);
})->where('slug', '[A-Za-z0-9\-]+');

// Old plural path /careers/{slug} → canonical /career/{slug}
Route::get('/careers/{slug}', function (string $slug) {
    return redirect()->route('jobs.show', ['slug' => $slug], 301);
})->where('slug', '[A-Za-z0-9\-]+');

/*
|--------------------------------------------------------------------------
| Public APIs
|--------------------------------------------------------------------------
*/
Route::get('/api/jobs/{role}', [App\Http\Controllers\Api\JobApiController::class, 'getJobDescription'])
    ->name('api.jobs.by-role');

Route::get('/api/job-description', [App\Http\Controllers\Api\JobApiController::class, 'getJobDescription'])
    ->name('api.job.description');

Route::get('/api/active-jobs', [App\Http\Controllers\Api\JobApiController::class, 'getActiveJobs'])
    ->name('api.jobs.active');

Route::get('/api/job/{slug}', [App\Http\Controllers\Api\JobApiController::class, 'getJobBySlug'])
    ->where('slug', '[A-Za-z0-9\-]+')
    ->name('api.job.by-slug');

/*
|--------------------------------------------------------------------------
| Candidate actions
|--------------------------------------------------------------------------
*/
// Apply to a specific job (by slug)
Route::post('/career/{slug}/apply', [CandidateController::class, 'store'])
    ->where('slug', '[A-Za-z0-9\-]+')
    ->name('candidate.store');

Route::get('/candidates/{id}', [CandidateController::class, 'show'])
    ->whereNumber('id')
    ->name('candidates.show');

Route::get('/candidate/dashboard', [CandidateController::class, 'dashboard'])
    ->name('candidate.dashboard');

Route::get('/download-resume/{id}', [CandidateController::class, 'downloadResume'])
    ->whereNumber('id')
    ->name('candidates.downloadResume');
Route::post('/jobs/{slug}/apply', [CandidateController::class, 'store'])->name('jobs.apply');
/*
|--------------------------------------------------------------------------
| Task submission
|--------------------------------------------------------------------------
*/
Route::get('/candidate/submit-task', fn () => view('candidate.submit-task'))
    ->name('submit.task.form');

Route::post('/candidate/submit-task', [TaskSubmissionController::class, 'submit'])
    ->name('submit.task');

/*
|--------------------------------------------------------------------------
| Utilities (Dev only)
|--------------------------------------------------------------------------
*/
Route::get('/check-task-requirement/{candidate_id}', function ($candidate_id) {
    $c = \App\Models\Candidate::find($candidate_id);
    if (!$c) return response()->json(['exists' => false]);
    return response()->json([
        'exists' => true,
        'has_task_id' => !is_null($c->task_id),
        'job_role' => $c->job_role,
    ]);
})->whereNumber('candidate_id');

Route::get('/test-jobs', function () {
    $jobs = \App\Models\Job::all(['id', 'title', 'slug', 'is_active']);
    echo "<h2>Available Jobs:</h2>";
    foreach ($jobs as $job) {
        $url = route('jobs.show', $job->slug);
        echo "<p><strong>ID:</strong> {$job->id} | <strong>Title:</strong> {$job->title} | <strong>Slug:</strong> {$job->slug} | <strong>Active:</strong> " . ($job->is_active ? 'Yes' : 'No') . "</p>";
        echo "<p><strong>Test URL:</strong> <a href='{$url}' target='_blank'>{$url}</a></p><hr>";
    }
});

// Basic application form (separate from existing /apply and candidate routes)
Route::get('/job-apply', [BasicApplicationController::class, 'create'])->name('basic-apply.form');
Route::post('/job-apply', [BasicApplicationController::class, 'store'])->name('basic-apply.store');
Route::get('/job-apply/thanks/{id}', [BasicApplicationController::class, 'thankYou'])->name('basic-apply.thanks');


// Resume Prompt (single textarea)
Route::get('/resume/prompt', [ResumePromptController::class, 'create'])->name('resume.prompt.form');
Route::post('/resume/prompt', [ResumePromptController::class, 'store'])->name('resume.prompt.store');
Route::get('/resume/prompt/thanks/{id}', [ResumePromptController::class, 'thankYou'])->name('resume.prompt.thanks');

//task upload routes
Route::get('/tasks/upload', [TaskUploadController::class, 'create'])->name('tasks.upload.form');
Route::post('/tasks/upload', [TaskUploadController::class, 'store'])->name('tasks.upload.store');
Route::get('/tasks/download/{id}', [TaskUploadController::class, 'download'])->name('tasks.upload.download');

Route::get('/lookup/candidate/{candidateId}', [TaskSubmissionController::class, 'lookupByCandidate']);






// use Illuminate\Support\Facades\Route;
// use App\Http\Controllers\CandidateController;
// use App\Http\Controllers\JobController;
// use App\Http\Controllers\TaskSubmissionController;

// // Landing & Static Pages
// Route::view('/', 'landing');
// Route::view('/apply', 'apply');


// // Job Routes for Dynamic Job Descriptions
// Route::get('/jobs', [JobController::class, 'index'])->name('jobs.index');
// Route::get('/jobs/{slug}', [JobController::class, 'show'])->name('jobs.show');

// // Dynamic routing for Indeed integration
// Route::get('/career/{role}', [JobController::class, 'showByRole'])->name('jobs.by-role');
// Route::get('/indeed/{indeedJobId}', [JobController::class, 'showByIndeedId'])->name('jobs.by-indeed-id');

// // API endpoints for job descriptions
// Route::get('/api/jobs/{role}', [JobController::class, 'apiGetJobByRole'])->name('api.jobs.by-role');
// Route::get('/api/job-description', [App\Http\Controllers\Api\JobApiController::class, 'getJobDescription'])->name('api.job.description');
// Route::get('/api/active-jobs', [App\Http\Controllers\Api\JobApiController::class, 'getActiveJobs'])->name('api.jobs.active');
// Route::get('/api/job/{slug}', [App\Http\Controllers\Api\JobApiController::class, 'getJobBySlug'])->name('api.job.by-slug');

// // Test route to see all jobs
// Route::get('/test-jobs', function() {
//     $jobs = App\Models\Job::all(['id', 'title', 'slug', 'is_active']);
//     echo "<h2>Available Jobs:</h2>";
//     foreach($jobs as $job) {
//         echo "<p><strong>ID:</strong> {$job->id} | <strong>Title:</strong> {$job->title} | <strong>Slug:</strong> {$job->slug} | <strong>Active:</strong> " . ($job->is_active ? 'Yes' : 'No') . "</p>";
//         echo "<p><strong>Test URL:</strong> <a href='/career/{$job->slug}' target='_blank'>http://127.0.0.1:8000/career/{$job->slug}</a></p><hr>";
//     }
// });

// // Candidate Application Form (legacy routes)
// Route::get('/candidate/apply', [CandidateController::class, 'create'])->name('candidate.form');
// Route::post('/candidate/apply', [CandidateController::class, 'store'])->name('candidate.submit');

// // Dashboard (Optional: if it's a thank-you or success page)
// Route::get('/candidate/dashboard', function () {
//     return view('candidate.dashboard'); // Make sure this view exists
// })->name('candidate.dashboard');

// // Resume Evaluation (AI logic)
// // Route::post('/evaluate-resume', [EvaluationController::class, 'evaluateResume'])->name('evaluate.resume');

// Route::get('/careers/{slug}', [CandidateController::class, 'showJob'])->name('careers.show');
// Route::post('/careers/{slug}/apply', [CandidateController::class, 'store'])->name('candidate.store');

// Route::get('/candidates/{id}', [CandidateController::class, 'show'])->name('candidates.show');

// Route::post('/jobs/{slug}/apply', [CandidateController::class, 'store'])->name('jobs.apply');

// Route::get('/candidate/dashboard', [CandidateController::class, 'dashboard'])->name('candidate.dashboard');

// Route::get('/download-resume/{id}', [CandidateController::class, 'downloadResume'])->name('candidates.downloadResume');


// Route::get('/candidate/submit-task', fn() => view('candidate.submit-task'))->name('submit.task.form');

// Route::post('/candidate/submit-task', [TaskSubmissionController::class, 'submit'])->name('submit.task');

// Route::get('/check-task-requirement/{candidate_id}', function ($candidate_id) {
//     $candidate = \App\Models\Candidate::find($candidate_id);

//     if (!$candidate) {
//         return response()->json(['exists' => false]);
//     }

//     return response()->json([
//         'exists' => true,
//         'has_task_id' => !is_null($candidate->task_id),
//         'job_role' => $candidate->job_role,
//     ]);
// });
