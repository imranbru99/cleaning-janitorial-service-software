<?php

namespace App\Filament\Resources\TenantModuleResource\Pages;

use App\Filament\Resources\TenantModuleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTenantModule extends EditRecord
{
    protected static string $resource = TenantModuleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
