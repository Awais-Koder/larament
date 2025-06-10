<?php

namespace App\Filament\Resources\FlaggedCustomerReasonResource\Pages;

use App\Filament\Resources\FlaggedCustomerReasonResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFlaggedCustomerReasons extends ListRecords
{
    protected static string $resource = FlaggedCustomerReasonResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
