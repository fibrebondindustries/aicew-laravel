<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DashboardStats extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            // Stat::make('Total Candidates', '1,234')
            //     ->description('32k increase')
            //     ->descriptionIcon('heroicon-m-arrow-trending-up')
            //     ->color('success'),
            // Stat::make('Active Evaluations', '56')
            //     ->description('3% decrease')
            //     ->descriptionIcon('heroicon-m-arrow-trending-down')
            //     ->color('danger'),
            // Stat::make('Completed Evaluations', '892')
            //     ->description('7% increase')
            //     ->descriptionIcon('heroicon-m-arrow-trending-up')
            //     ->color('success'),
            // Stat::make('Average Score', '80')
            //     ->description('0.2 increase')
            //     ->descriptionIcon('heroicon-m-arrow-trending-up')
            //     ->color('info'),
        ];
    }
} 