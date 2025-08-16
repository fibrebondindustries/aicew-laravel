<?php

namespace App\Filament\Widgets;

use App\Models\BasicApplication;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Session;

class DashboardFilterBar extends Widget
{
    protected static string $view = 'filament.widgets.dashboard-filter-bar';

    // Public state bound to the Blade inputs
    public ?string $filter_job_role = null;
    public ?string $filter_job_id   = null;
    public ?array  $filter_posted   = ['from' => null, 'to' => null];

    // Make sure this renders above other header widgets (lower sort = earlier)
    protected static ?int $sort = -100;

    public function mount(): void
    {
        // Restore any previously stored filters
        $stored = Session::get('dash_filters', []);
        $this->filter_job_role = $stored['job_role'] ?? null;
        $this->filter_job_id   = $stored['job_id']   ?? null;
        $this->filter_posted   = $stored['posted']   ?? ['from' => null, 'to' => null];
    }

    public function getJobRoleOptions(): array
    {
        return BasicApplication::query()
            ->whereNotNull('job_role')
            ->distinct()
            ->orderBy('job_role')
            ->pluck('job_role', 'job_role')
            ->toArray();
    }

    /** Persist & notify stats widget whenever a filter changes */
    public function updated($prop): void
    {
        Session::put('dash_filters', [
            'job_role' => $this->filter_job_role,
            'job_id'   => $this->filter_job_id,
            'posted'   => $this->filter_posted,
        ]);

        // Livewire v3 event to refresh the stats widget
        $this->dispatch('dashFiltersUpdated');
    }
}
