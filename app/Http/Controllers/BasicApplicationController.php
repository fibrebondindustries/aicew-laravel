<?php

// app/Http/Controllers/BasicApplicationController.php
namespace App\Http\Controllers;

use App\Models\BasicApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BasicApplicationController extends Controller
{
    public function create()
    {
        return view('candidate.basic-apply'); // Blade below
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name'           => ['required','string','max:120'],
            'email'               => ['required','email','max:150'],
            'mobile'              => ['required','string','max:20'],
            'gender'              => ['nullable','string','max:20'],
            'location'            => ['nullable','string','max:150'],
            'years_of_experience' => ['nullable','numeric','min:0','max:99.9'],
            'current_salary'      => ['nullable','numeric','min:0'],
            'expected_salary'     => ['nullable','numeric','min:0'],
            'notice_period'       => ['nullable','string','max:50'],
            'portfolio_link'      => ['nullable','url','max:255'],
            'resume'              => ['required','file','mimes:pdf,doc,docx','max:5120'], // 5 MB
        ]);

        $app = DB::transaction(function () use ($request, $validated) {
            // Generate FBI### safely under lock
            $last = BasicApplication::where('candidate_id', 'LIKE', 'FBI%')
                ->orderByRaw("CAST(SUBSTRING(candidate_id, 4) AS UNSIGNED) DESC")
                ->lockForUpdate()
                ->first();

            $nextNumber = 101;
            if ($last && preg_match('/FBI(\d+)/', $last->candidate_id, $m)) {
                $nextNumber = ((int)$m[1]) + 1;
            }
            $candidateId = 'FBI' . $nextNumber;

            // Store resume under candidate-specific folder on public disk
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
            ]);
        });

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
