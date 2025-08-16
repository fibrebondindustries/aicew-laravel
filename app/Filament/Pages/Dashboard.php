<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use App\Filament\Widgets;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static string $view = 'filament.pages.dashboard';
    protected static ?int $navigationSort = -2;

    public function getTitle(): string
    {
        return 'Dashboard';
    }

    protected function getHeaderWidgets(): array
    {
        return [
            Widgets\DashboardFilterBar::class,    // ⬅️ filter bar in the area you marked
            Widgets\CandidateStatsWidget::class,  // ⬅️ stats below it
            // Widgets\DashboardStats::class,      // (optional) your extra widget
        ];
    }
}
