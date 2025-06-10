<?php

namespace App\Filament\Resources\StorageCompanyResource\Pages;

use App\Filament\Resources\StorageCompanyResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStorageCompany extends EditRecord
{
    protected static string $resource = StorageCompanyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
