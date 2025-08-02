<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Job;
use Illuminate\Http\Request;

class JobApiController extends Controller
{
    /**
     * Get job description by role/title
     */
    public function getJobDescription(Request $request)
    {
        $role = $request->get('role');
        
        if (!$role) {
            return response()->json(['error' => 'Job role is required'], 400);
        }

        $job = Job::where('title', 'LIKE', "%{$role}%")
                  ->orWhere('slug', 'LIKE', "%{$role}%")
                  ->where('is_active', true)
                  ->first();

        if (!$job) {
            return response()->json(['error' => 'Job not found'], 404);
        }

        return response()->json([
            'success' => true,
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
            ]
        ]);
    }

    /**
     * Get all active jobs
     */
    public function getActiveJobs()
    {
        $jobs = Job::where('is_active', true)
                   ->select('id', 'title', 'slug', 'location', 'type', 'experience_level')
                   ->get();

        return response()->json([
            'success' => true,
            'jobs' => $jobs
        ]);
    }

    /**
     * Get job by slug
     */
    public function getJobBySlug($slug)
    {
        $job = Job::where('slug', $slug)
                  ->where('is_active', true)
                  ->first();

        if (!$job) {
            return response()->json(['error' => 'Job not found'], 404);
        }

        return response()->json([
            'success' => true,
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
            ]
        ]);
    }
} 