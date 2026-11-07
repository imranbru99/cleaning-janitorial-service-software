<?php

namespace App\Filament\Resources\CleaningJobResource\Pages;

use App\Filament\Resources\CleaningJobResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCleaningJob extends EditRecord
{
    protected static string $resource = CleaningJobResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
