<?php

namespace App\Filament\Resources\CleaningJobResource\Pages;

use App\Filament\Resources\CleaningJobResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCleaningJobs extends ListRecords
{
    protected static string $resource = CleaningJobResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
