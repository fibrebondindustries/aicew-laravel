<?php

namespace App\Filament\Resources\ResumePromptResource\Pages;

use App\Filament\Resources\ResumePromptResource;
use Filament\Resources\Pages\ListRecords;

class ListResumePrompts extends ListRecords
{
    protected static string $resource = ResumePromptResource::class;
    protected function getHeaderActions(): array
    {
        return []; // read-only (hide Create)
    }
}
