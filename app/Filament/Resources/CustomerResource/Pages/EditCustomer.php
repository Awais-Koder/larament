<?php

namespace App\Filament\Resources\CustomerResource\Pages;

use App\Filament\Resources\CustomerResource;
use App\Models\FlaggedCustomerReason;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCustomer extends EditRecord
{
    protected static string $resource = CustomerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
    protected function afterSave(): void
    {
        $customer = $this->record;

        if ($this->data['is_flagged'] ?? false) {
            FlaggedCustomerReason::create([
                'customer_id' => $customer->id,
                'reason' => $this->data['flagged_reason'] ?? 'Flagged without reason',
                'action_by' => auth()->id(),
                'action_type' => 'flagged',
            ]);
        } else {
            FlaggedCustomerReason::create([
                'customer_id' => $customer->id,
                'reason' => 'Customer unflagged',
                'action_by' => auth()->id(),
                'action_type' => 'unflagged',
            ]);
        }
    }
    
}
