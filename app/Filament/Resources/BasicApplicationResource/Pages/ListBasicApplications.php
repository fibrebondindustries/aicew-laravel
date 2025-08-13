<?php

namespace App\Filament\Resources\BasicApplicationResource\Pages;

use App\Filament\Resources\BasicApplicationResource;
use Filament\Resources\Pages\ListRecords;

class ListBasicApplications extends ListRecords
{
    protected static string $resource = BasicApplicationResource::class;
    protected function getHeaderActions(): array
    {
        return []; // read-only listing
    }
}
