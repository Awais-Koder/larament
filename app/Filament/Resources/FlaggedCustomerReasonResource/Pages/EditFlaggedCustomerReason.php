<?php

namespace App\Filament\Resources\FlaggedCustomerReasonResource\Pages;

use App\Filament\Resources\FlaggedCustomerReasonResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFlaggedCustomerReason extends EditRecord
{
    protected static string $resource = FlaggedCustomerReasonResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
