<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

class EvaluationController extends Controller
{
    public function evaluateResume(Request $request)
    {
        $response = Http::withOptions([
                'verify' => storage_path('certs/cacert.pem'),
            ])
            ->attach(
                'resume', file_get_contents($request->file('resume')), $request->file('resume')->getClientOriginalName()
            )
            ->post('https://aicew.fibrebondindustries.com/evaluate-resume', [
                'candidate_id' => $request->candidate_id,
                'job_role' => $request->job_role,
                'jd' => $request->jd,
                'experience' => $request->experience,
            ]);

        if ($response->successful()) {
            return back()->with('success', 'Resume evaluated successfully: ' . json_encode($response->json()));
        }

        return back()->withErrors('Error: ' . $response->body());
    }
}
