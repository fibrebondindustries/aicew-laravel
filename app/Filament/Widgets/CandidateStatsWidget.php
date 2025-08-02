<?php

namespace App\Filament\Widgets;

use App\Models\Candidate;
use App\Models\Job;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CandidateStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $totalCandidates = Candidate::count();
        $evaluatedCandidates = Candidate::whereNotNull('score')->count();
        $highScoreCandidates = Candidate::where('score', '>=', 80)->count();
        $activeJobs = Job::where('is_active', true)->count();

        return [
            Stat::make('Total Applications', $totalCandidates)
                ->description('All time applications')
                ->descriptionIcon('heroicon-m-users')
                ->color('info'),
            
            Stat::make('Evaluated Candidates', $evaluatedCandidates)
                ->description('With AICEW scores')
                ->descriptionIcon('heroicon-m-clipboard-document-check')
                ->color('success'),
            
            Stat::make('High Score (80+)', $highScoreCandidates)
                ->description('Excellent candidates')
                ->descriptionIcon('heroicon-m-star')
                ->color('warning'),
            
            Stat::make('Active Jobs', $activeJobs)
                ->description('Currently posted')
                ->descriptionIcon('heroicon-m-briefcase')
                ->color('primary'),
        ];
    }
} 