<?php

namespace App\Http\Controllers;

use App\Models\ResumePrompt;
use App\Models\Job;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class TaskUploadController extends Controller
{
    public function create()
    {
        // Your jobs table has job_id (string) + title
        $jobs = ResumePrompt::select('job_id', 'title')->where('is_active', 1)->orderByDesc('job_id')->get();

        return view('tasks.upload', ['jobs' => $jobs]);
    }

public function store(Request $request)
{
    $request->validate([
        'job_id'       => ['required', 'string'],

        // NEW: mode selector; prompt stays optional
        'task_mode'     => ['required', 'in:ai,manual'],
        'task_prompt'        => ['nullable', 'string'],

        'task_files'     => ['nullable', 'array'],
        'task_files.*' => ['file', 'mimes:pdf,doc,docx,csv,xlsx', 'max:20480'],
         'task_link'      => ['nullable', 'url'],             
    ]);

    $hasFiles = $request->hasFile('task_files') && count($request->file('task_files')) > 0;
    $hasLink  = filled($request->task_link);

    if (! $hasFiles && ! $hasLink) {
        return back()
            ->withErrors([
                'task_files' => 'Upload at least one file or enter a task link.',
                'task_link'  => 'Upload at least one file or enter a task link.',
            ])->withInput();
    }


    $jobId = $request->job_id;

    // Find existing row by unique job_id or create
    $rp = \App\Models\ResumePrompt::where('job_id', $jobId)->first();

    if (!$rp) {
        $rp = new \App\Models\ResumePrompt();
        $rp->job_id    = $jobId;
        $rp->is_active = true;

        // Your table likely has NOT NULL title/prompt → set empty once
        $rp->title     = '';
        $rp->prompt    = '';

        // initialize JSON fields
        $rp->task_file_path = json_encode([]);
        $rp->task_file_name = json_encode([]);
        $rp->task_file_mime = json_encode([]);
        $rp->task_file_size = json_encode([]);
    }

      // ✅ NEW: store mode and prompt (prompt is optional)
        $rp->task_mode = in_array($request->task_mode, ['ai', 'manual'], true)
            ? $request->task_mode
            : ($rp->task_mode ?? 'manual');

        if ($request->filled('task_prompt')) {
            $rp->task_prompt = $request->task_prompt;
        }

    // Decode existing arrays (tolerate legacy CSV if any)
    $paths = $this->toArray($rp->task_file_path);
    $names = $this->toArray($rp->task_file_name);
    $mimes = $this->toArray($rp->task_file_mime);
    $sizes = $this->toArray($rp->task_file_size);
    // ✅ only loop if files exist
    if ($hasFiles) {
        foreach ($request->file('task_files') as $file) {
            $storedPath = $file->store('resume-tasks/' . $jobId, 'public');

            $paths[] = $storedPath;
            $names[] = $file->getClientOriginalName();
            $mimes[] = $file->getClientMimeType();
            $sizes[] = $file->getSize(); // keep numeric inside JSON
        }
    }
    // Save as JSON arrays
    $rp->task_file_path = json_encode($paths);
    $rp->task_file_name = json_encode($names);
    $rp->task_file_mime = json_encode($mimes);
    $rp->task_file_size = json_encode($sizes);
    $rp->task_link      = $hasLink ? $request->task_link : null; // ← store single link
    $rp->save();

    return back()->with('status', 'Task files uploaded successfully.');
}

/**
 * Helper: robustly convert stored value to array (supports JSON or legacy CSV).
 */
// private function toArray($value): array
// {
//     if (empty($value)) return [];
//     $decoded = json_decode($value, true);
//     if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
//         return $decoded;
//     }
//     // fallback if old data was comma/pipe separated
//     if (is_string($value)) {
//         // try pipe first, then comma
//         if (str_contains($value, '|')) return array_filter(explode('|', $value), 'strlen');
//         if (str_contains($value, ',')) return array_filter(explode(',', $value), 'strlen');
//     }
//     return [];
// }


// TaskUploadController.php

/**
 * Helper: normalize any stored value to an array.
 * Accepts: already-an-array, JSON string, CSV (| or ,), single string, or null.
 */
private function toArray($value): array
{
    // Already an array (because of model casts)
    if (is_array($value)) {
        return array_values($value);
    }

    // Null / empty
    if ($value === null || $value === '' ) {
        return [];
    }

    // Strings: try JSON first
    if (is_string($value)) {
        $trimmed = trim($value);
        if ($trimmed === '') {
            return [];
        }

        $decoded = json_decode($trimmed, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            return is_array($decoded) ? array_values($decoded) : [$decoded];
        }

        // CSV fallbacks
        if (str_contains($trimmed, '|')) {
            return array_values(array_filter(explode('|', $trimmed), 'strlen'));
        }
        if (str_contains($trimmed, ',')) {
            return array_values(array_filter(explode(',', $trimmed), 'strlen'));
        }

        // Plain single string
        return [$trimmed];
    }

    // Anything else → empty
    return [];
}




    public function download($id)
    {
        $resumePrompt = ResumePrompt::findOrFail($id);

        if (!$resumePrompt->task_file_path || !Storage::disk('public')->exists($resumePrompt->task_file_path)) {
            abort(404, 'File not found.');
        }

        return Storage::disk('public')->download(
            $resumePrompt->task_file_path,
            $resumePrompt->task_file_name
        );
    }
}
