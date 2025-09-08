<?php

namespace App\Filament\Resources\ResumePromptResource\Pages;

use App\Filament\Resources\ResumePromptResource;
use App\Models\ResumePrompt;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;

class CreateResumePrompt extends CreateRecord
{
    protected static string $resource = ResumePromptResource::class;

    // Auto-generate JOB# safely
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        DB::transaction(function () use (&$data) {
            $last = ResumePrompt::where('job_id', 'LIKE', 'JOB%')
                ->orderByRaw("CAST(SUBSTRING(job_id, 4) AS UNSIGNED) DESC")
                ->lockForUpdate()
                ->first();

            $next = 1;
            if ($last && preg_match('/JOB(\d+)/', $last->job_id, $m)) {
                $next = ((int) $m[1]) + 1;
            }

            $data['job_id'] = 'JOB' . $next;
        });

        return $data;
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Job created';
    }
}
