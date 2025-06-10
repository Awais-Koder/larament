<?php

namespace App\Filament\Resources\StorageFacilityResource\Pages;

use App\Filament\Resources\StorageFacilityResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStorageFacilities extends ListRecords
{
    protected static string $resource = StorageFacilityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
