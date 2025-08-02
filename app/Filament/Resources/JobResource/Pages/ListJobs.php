<?php

namespace App\Filament\Resources\JobResource\Pages;

use App\Filament\Resources\JobResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\Exports\ExportBulkAction;
use App\Exports\JobsExport;

class ListJobs extends ListRecords
{
    protected static string $resource = JobResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            // You can add widgets here if needed
        ];
    }

    protected function getTableBulkActions(): array
    {
        return [
            ExportBulkAction::make()
                ->label('Export Selected Jobs')
                ->exporter(JobsExport::class)
                ->icon('heroicon-o-arrow-down-tray'),
        ];
    }
} 