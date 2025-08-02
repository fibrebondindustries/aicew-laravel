<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Candidate extends Model
{
    use HasFactory;

     protected $primaryKey = 'candidate_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'candidate_id',
        'name',
        'job_role',
        'experience',
        'resume',
        'email',
        'phone',
        'score',
        'summary',
        'job_id',
        'task_title',
        'task_description',
        'expected_output',
        'task_id',
    ];

    protected $casts = [
        'score' => 'decimal:2',
    ];

    // Optionally relate to JobPosting if needed in future
    // public function jobPosting()
    // {
    //     return $this->belongsTo(JobPosting::class, 'job_role', 'slug');
    // }
    public function job()
    {
        return $this->belongsTo(Job::class);
    }

    public function getFormattedScoreAttribute()
    {
        return $this->score ? number_format($this->score, 1) . '/10' : 'Not evaluated';
    }

    public function getEvaluationStatusAttribute()
    {
        if ($this->score >= 8) return 'Excellent';
        if ($this->score >= 6) return 'Good';
        if ($this->score >= 4) return 'Fair';
        return 'Poor';
    }
}
