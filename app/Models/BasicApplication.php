<?php

// app/Models/BasicApplication.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BasicApplication extends Model
{
    protected $fillable = [
        'candidate_id',
            'job_id',                // <-- add this
        'full_name','email','mobile','gender','location',
        'years_of_experience','current_salary','expected_salary',
        'notice_period','portfolio_link','resume_path',
    ];
}
