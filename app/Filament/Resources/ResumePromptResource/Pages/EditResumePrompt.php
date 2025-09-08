<?php

namespace App\Filament\Resources\ResumePromptResource\Pages;

use App\Filament\Resources\ResumePromptResource;
use Filament\Resources\Pages\EditRecord;

class EditResumePrompt extends EditRecord
{
    protected static string $resource = ResumePromptResource::class;

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Job updated';
    }
}
