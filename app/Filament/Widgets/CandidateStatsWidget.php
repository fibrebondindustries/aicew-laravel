<?php

namespace App\Filament\Widgets;

use App\Models\BasicApplication;
use App\Models\ResumePrompt;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CandidateStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        // Applications
        $totalApplications   = BasicApplication::count();
        $last7Days           = BasicApplication::where('created_at', '>=', now()->subDays(7))->count();

        // AI scoring (same thresholds used in BasicApplicationResource)
        $withScore           = BasicApplication::whereNotNull('ai_score')->count();
        $shortlisted         = BasicApplication::whereNotNull('ai_score')
                                ->whereBetween('ai_score', [80, 89.9999])->count();
        $highlyRecommended   = BasicApplication::where('ai_score', '>=', 90)->count();
        $avgScore            = BasicApplication::whereNotNull('ai_score')->avg('ai_score');

        // Active jobs (from ResumePrompt)
        $activeJobs          = ResumePrompt::where('is_active', true)->count();

        return [
            Stat::make('Total Applications', number_format($totalApplications))
                ->description("Last 7 days: {$last7Days}")
                ->descriptionIcon('heroicon-m-inbox-arrow-down')
                ->color('info'),

       
            Stat::make('Shortlisted (80–89)', number_format($shortlisted))
                ->description('Meets criteria')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('warning'),

            Stat::make('Highly Recommended (90+)', number_format($highlyRecommended))
                ->description('Top candidates')
                ->descriptionIcon('heroicon-m-star')
                ->color('success'),

        

            Stat::make('Active Jobs', number_format($activeJobs))
                ->description('Currently posted')
                ->descriptionIcon('heroicon-m-briefcase')
                ->color('primary'),
        ];
    }
}
