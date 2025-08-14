<?php

// app/Models/ResumePrompt.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResumePrompt extends Model
{
    protected $fillable = ['job_id', 'title', 'prompt', 'is_active'];

     protected $casts = [
        'is_active' => 'boolean',
    ];
}
