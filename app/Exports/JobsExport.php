<?php

namespace App\Exports;

use App\Models\Job;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;

class JobsExport extends Exporter
{
    protected static ?string $model = Job::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('title')
                ->label('Job Title'),
            
            ExportColumn::make('slug')
                ->label('Slug'),
            
            ExportColumn::make('location')
                ->label('Location'),
            
            ExportColumn::make('type')
                ->label('Job Type'),
            
            ExportColumn::make('experience_level')
                ->label('Experience Level'),
            
            ExportColumn::make('formatted_salary')
                ->label('Salary'),
            
            ExportColumn::make('is_active')
                ->label('Active')
                ->formatStateUsing(fn ($state) => $state ? 'Yes' : 'No'),
            
            ExportColumn::make('candidates_count')
                ->label('Applications')
                ->counts('candidates'),
            
            ExportColumn::make('created_at')
                ->label('Posted On')
                ->formatStateUsing(fn ($state) => $state->format('Y-m-d H:i:s')),
        ];
    }

    public static function getFileName(): string
    {
        return 'jobs_' . date('Y-m-d_H-i-s');
    }
} 