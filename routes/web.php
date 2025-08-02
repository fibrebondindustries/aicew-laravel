<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CandidateController;
use App\Http\Controllers\EvaluationController;
use App\Http\Controllers\JobController;

// Landing & Static Pages
Route::view('/', 'landing');
Route::view('/apply', 'apply');
// Route::view('/admin/login', 'admin.login');

// Job Routes for Dynamic Job Descriptions
Route::get('/jobs', [JobController::class, 'index'])->name('jobs.index');
Route::get('/jobs/{slug}', [JobController::class, 'show'])->name('jobs.show');

// Dynamic routing for Indeed integration
Route::get('/career/{role}', [JobController::class, 'showByRole'])->name('jobs.by-role');
Route::get('/indeed/{indeedJobId}', [JobController::class, 'showByIndeedId'])->name('jobs.by-indeed-id');

// API endpoints for job descriptions
Route::get('/api/jobs/{role}', [JobController::class, 'apiGetJobByRole'])->name('api.jobs.by-role');
Route::get('/api/job-description', [App\Http\Controllers\Api\JobApiController::class, 'getJobDescription'])->name('api.job.description');
Route::get('/api/active-jobs', [App\Http\Controllers\Api\JobApiController::class, 'getActiveJobs'])->name('api.jobs.active');
Route::get('/api/job/{slug}', [App\Http\Controllers\Api\JobApiController::class, 'getJobBySlug'])->name('api.job.by-slug');

// Test route to see all jobs
Route::get('/test-jobs', function() {
    $jobs = App\Models\Job::all(['id', 'title', 'slug', 'is_active']);
    echo "<h2>Available Jobs:</h2>";
    foreach($jobs as $job) {
        echo "<p><strong>ID:</strong> {$job->id} | <strong>Title:</strong> {$job->title} | <strong>Slug:</strong> {$job->slug} | <strong>Active:</strong> " . ($job->is_active ? 'Yes' : 'No') . "</p>";
        echo "<p><strong>Test URL:</strong> <a href='/career/{$job->slug}' target='_blank'>http://127.0.0.1:8000/career/{$job->slug}</a></p><hr>";
    }
});

// Candidate Application Form (legacy routes)
Route::get('/candidate/apply', [CandidateController::class, 'create'])->name('candidate.form');
Route::post('/candidate/apply', [CandidateController::class, 'store'])->name('candidate.submit');

// Dashboard (Optional: if it's a thank-you or success page)
Route::get('/candidate/dashboard', function () {
    return view('candidate.dashboard'); // Make sure this view exists
})->name('candidate.dashboard');

// Resume Evaluation (AI logic)
Route::post('/evaluate-resume', [EvaluationController::class, 'evaluateResume'])->name('evaluate.resume');

Route::get('/careers/{slug}', [CandidateController::class, 'showJob'])->name('careers.show');
Route::post('/careers/{slug}/apply', [CandidateController::class, 'store'])->name('candidate.store');

Route::get('/candidates/{id}', [CandidateController::class, 'show'])->name('candidates.show');

Route::post('/jobs/{slug}/apply', [CandidateController::class, 'store'])->name('jobs.apply');

Route::get('/candidate/dashboard', [CandidateController::class, 'dashboard'])->name('candidate.dashboard');

Route::get('/download-resume/{id}', [CandidateController::class, 'downloadResume'])->name('candidates.downloadResume');