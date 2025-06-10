<?php

namespace App\Filament\Resources\CustomerResource\Pages;

use App\Filament\Resources\CustomerResource;
use Filament\Actions;
use App\Models\FlaggedCustomerReason;
use Filament\Resources\Pages\CreateRecord;
use App\Models\Customer;

class CreateCustomer extends CreateRecord
{
    protected static string $resource = CustomerResource::class;

    protected function afterCreate(): void
    {
        $customer = $this->record;

        if ($this->data['is_flagged'] ?? false) {
            FlaggedCustomerReason::create([
                'customer_id' => $customer->id,
                'reason' => $this->data['flagged_reason'] ?? 'Flagged without reason',
                'action_by' => auth()->id(),
                'action_type' => 'flagged',
            ]);
        }
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();
        return $data;
    }

}

