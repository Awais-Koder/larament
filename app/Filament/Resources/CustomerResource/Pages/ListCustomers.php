<?php

namespace App\Filament\Resources\CustomerResource\Pages;

use App\Filament\Resources\CustomerResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Models\FlaggedCustomerReason;
use Filament\Notifications\Notification;


class ListCustomers extends ListRecords
{
    protected static string $resource = CustomerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
    // public function updatedTableSearch(): void
    // {
    //     $search = $this->getTableSearch();

    //     if (
    //         auth()->user()?->hasRole('storage_manager') &&
    //         filled($search)
    //     ) {
    //         $found = \App\Models\Customer::query()
    //             ->where('is_flagged', 1)
    //             ->where(function ($query) use ($search) {
    //                 $query->where('rsa_id', $search)
    //                         ->orWhere('email', $search);
    //             })
    //             ->exists();

    //         if (! $found) {
    //             Notification::make()
    //                 ->title('No customer found with the provided RSA ID or email.')
    //                 ->warning()
    //                 // ->persistent()
    //                 ->send();
    //         }
    //     }
    // }
}
