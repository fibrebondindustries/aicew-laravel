<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Job extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_id',
        'title',
        'slug',
        'description',
        'requirements',
        'responsibilities',
        'location',
        'type',
        'experience_level',
        'salary_min',
        'salary_max',
        'salary_currency',
        'is_active',
        'indeed_job_id',
        'indeed_apply_url',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'salary_min' => 'decimal:2',
        'salary_max' => 'decimal:2',
    ];

    // Boot method to auto-generate slug
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($job) {
            // if (empty($job->slug)) {
            //     $job->slug = Str::slug($job->title);
            // }

            // Auto-generate job_id like JOB101, JOB102...
            if (empty($job->job_id)) {
            $lastJob = self::orderByDesc('job_id')->first();
            $lastNumber = 100;

            if ($lastJob && preg_match('/JOB(\d+)/', $lastJob->job_id, $matches)) {
                $lastNumber = (int) $matches[1];
            }

            $job->job_id = 'JOB' . ($lastNumber + 1);
        }

        // ✅ Generate slug + unique suffix (like PY001, PY002)
        // Always generate a new unique slug (even for same titles)
        $baseSlug = Str::slug($job->title);
        $existingCount = self::where('slug', 'LIKE', "{$baseSlug}-PY%")->count() + 1;
        $uniqueSuffix = 'PY' . str_pad($existingCount, 3, '0', STR_PAD_LEFT);

        $job->slug = "{$baseSlug}-{$uniqueSuffix}";

 });

        
    }

    // Scope for active jobs
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Get formatted salary range
    public function getFormattedSalaryAttribute()
    {
        if ($this->salary_min && $this->salary_max) {
            return $this->salary_currency . ' ' . number_format($this->salary_min) . ' - ' . number_format($this->salary_max);
        } elseif ($this->salary_min) {
            return $this->salary_currency . ' ' . number_format($this->salary_min) . '+';
        }
        return 'Competitive';
    }

    // Get route key name for URL generation
    public function getRouteKeyName()
    {
        return 'slug';
    }

    // Relationship with candidates (if you want to track applications)
   public function candidates()
    {
        return $this->hasMany(Candidate::class);
    }

}
