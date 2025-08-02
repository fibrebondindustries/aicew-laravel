<?php

namespace App\Filament\Resources\CandidateResource\Pages;

use App\Filament\Resources\CandidateResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\Exports\ExportBulkAction;
use App\Exports\CandidatesExport;

class ListCandidates extends ListRecords
{
    protected static string $resource = CandidateResource::class;

    // protected function getHeaderActions(): array
    // {
    //     return [
    //         Actions\CreateAction::make(),
    //     ];
    // }

    protected function getHeaderWidgets(): array
    {
        return [
            // You can add widgets here for candidate statistics
        ];
    }

    protected function getTableBulkActions(): array
    {
        return [
            ExportBulkAction::make()
                ->label('Export Selected Candidates')
                ->exporter(CandidatesExport::class)
                ->icon('heroicon-o-arrow-down-tray'),
        ];
    }
    protected function canCreate(): bool
    {
        return false;
    }

}
