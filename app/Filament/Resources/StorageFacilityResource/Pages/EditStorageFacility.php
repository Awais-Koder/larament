<?php

namespace App\Filament\Resources\StorageFacilityResource\Pages;

use App\Filament\Resources\StorageFacilityResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStorageFacility extends EditRecord
{
    protected static string $resource = StorageFacilityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
