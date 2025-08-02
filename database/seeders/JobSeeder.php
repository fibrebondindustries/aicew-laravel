<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Job;

class JobSeeder extends Seeder
{
    public function run(): void
    {
        $jobs = [
            [
                'title' => 'Data Analyst',
                'slug' => 'data-analyst',
                'description' => 'We are seeking a skilled Data Analyst to join our team...',
                'requirements' => "• Bachelor's degree in Statistics...\n• 2+ years of experience...",
                'responsibilities' => "• Collect, clean, and analyze large datasets...",
                'location' => 'Remote / New York, NY',
                'type' => 'Full-time',
                'experience_level' => 'Mid',
                'salary_min' => 70000,
                'salary_max' => 90000,
                'salary_currency' => 'INR',
                'is_active' => true,
                'indeed_job_id' => 'data-analyst-001',
                'indeed_apply_url' => 'http://127.0.0.1:8000/career/data-analyst',
            ],
            [
                'title' => 'Full Stack Developer',
                'slug' => 'full-stack-developer',
                'description' => 'We are looking for a talented Full Stack Developer...',
                'requirements' => "• Bachelor's degree in Computer Science...\n• 3+ years experience...",
                'responsibilities' => "• Develop and maintain web applications...",
                'location' => 'Remote / San Francisco, CA',
                'type' => 'Full-time',
                'experience_level' => 'Senior',
                'salary_min' => 100000,
                'salary_max' => 130000,
                'salary_currency' => 'INR',
                'is_active' => true,
                'indeed_job_id' => 'full-stack-dev-001',
                'indeed_apply_url' => 'http://127.0.0.1:8000/career/full-stack-developer',
            ],
            [
                'title' => 'Frontend Developer',
                'slug' => 'frontend-developer',
                'description' => 'Join our team as a Frontend Developer...',
                'requirements' => "• 2+ years of frontend development experience...",
                'responsibilities' => "• Build responsive and accessible UIs...",
                'location' => 'Remote / Austin, TX',
                'type' => 'Full-time',
                'experience_level' => 'Mid',
                'salary_min' => 75000,
                'salary_max' => 95000,
                'salary_currency' => 'INR',
                'is_active' => true,
                'indeed_job_id' => 'frontend-dev-001',
                'indeed_apply_url' => 'http://127.0.0.1:8000/career/frontend-developer',
            ],
        ];

        foreach ($jobs as $job) {
            Job::updateOrInsert(
                ['slug' => $job['slug']],  // condition to prevent duplicates
                array_merge($job, [
                    'updated_at' => now(),
                    'created_at' => now(),
                ])
            );
        }
    }
}