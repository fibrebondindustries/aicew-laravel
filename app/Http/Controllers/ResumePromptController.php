<?php

// app/Http/Controllers/ResumePromptController.php
namespace App\Http\Controllers;

use App\Models\ResumePrompt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ResumePromptController extends Controller
{
    public function create()
    {
        // shows the page with one textarea
        return view('resume.prompt');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
             'title'  => 'required|string|max:255',
            'prompt' => ['required','string','min:5'],
        ]);

        $record = DB::transaction(function () use ($validated) {
            // Find latest JOB# and lock row set to avoid race conditions
            $last = ResumePrompt::where('job_id', 'LIKE', 'JOB%')
                ->orderByRaw("CAST(SUBSTRING(job_id, 4) AS UNSIGNED) DESC")
                ->lockForUpdate()
                ->first();

            $nextNumber = 1;
            if ($last && preg_match('/JOB(\d+)/', $last->job_id, $m)) {
                $nextNumber = ((int)$m[1]) + 1;
            }
            $jobCode = 'JOB' . $nextNumber;

            return ResumePrompt::create([
                'job_id' => $jobCode,
                'title'  => $validated['title'],
                'prompt'   => $validated['prompt'],
            ]);
        });

        return redirect()
            ->route('resume.prompt.thanks', ['id' => $record->id])
            ->with('status', "Saved. Your Job ID is {$record->job_id}.");
    }

    public function thankYou($id)
    {
        $record = ResumePrompt::findOrFail($id);
        return view('resume.prompt-thanks', compact('record'));
    }
}
