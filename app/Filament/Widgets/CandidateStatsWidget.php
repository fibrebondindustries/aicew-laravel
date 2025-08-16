<?php

namespace App\Filament\Widgets;

use App\Models\BasicApplication;
use App\Models\ResumePrompt;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\On;

class CandidateStatsWidget extends BaseWidget
{
    // Local copy of filters (loaded from session)
    public ?string $filter_job_role = null;
    public ?string $filter_job_id   = null;
    public ?array  $filter_posted   = ['from' => null, 'to' => null];

    protected function getColumns(): int
    {
        return 4;
    }

    public function mount(): void
    {
        $this->reloadFilters();
    }

    #[On('dashFiltersUpdated')]
    public function reloadFilters(): void
    {
        $stored = Session::get('dash_filters', []);
        $this->filter_job_role = $stored['job_role'] ?? null;
        $this->filter_job_id   = $stored['job_id']   ?? null;
        $this->filter_posted   = $stored['posted']   ?? ['from' => null, 'to' => null];

        // Force a re-render of the widget after updating state
        $this->dispatch('$refresh');
    }

    protected function applyFilters(Builder $query): Builder
    {
        if (!empty($this->filter_job_role)) {
            $query->where('job_role', $this->filter_job_role);
        }
        if (!empty($this->filter_job_id)) {
            $query->where('job_id', $this->filter_job_id);
        }

        $from = $this->filter_posted['from'] ?? null;
        $to   = $this->filter_posted['to']   ?? null;

        if (!empty($from)) {
            $query->where('created_at', '>=', Carbon::parse($from)->startOfDay());
        }
        if (!empty($to)) {
            $query->where('created_at', '<=', Carbon::parse($to)->endOfDay());
        }

        return $query;
    }

    protected function getStats(): array
    {
        $base = $this->applyFilters(BasicApplication::query());

        $totalApplications = (clone $base)->count();
        $last7Days = $this->applyFilters(
            BasicApplication::query()->where('created_at', '>=', now()->subDays(7))
        )->count();

        $shortlisted = (clone $base)
            ->whereNotNull('ai_score')
            ->whereBetween('ai_score', [80, 89.9999])
            ->count();

        $highlyRecommended = (clone $base)
            ->where('ai_score', '>=', 90)
            ->count();

        $activeJobs = ResumePrompt::where('is_active', true)->count();

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
