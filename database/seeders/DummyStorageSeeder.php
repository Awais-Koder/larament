<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\StorageCompany;
use App\Models\StorageFacility;
use App\Models\Customer;

class DummyStorageSeeder extends Seeder
{
    public function run(): void
    {
        $companies = StorageCompany::factory()->count(2)->create(['verified' => true]);

        $companies->each(function ($company) {
            $facilities = StorageFacility::factory()->count(2)->create([
                'storage_company_id' => $company->id,
            ]);

            $facilities->each(function ($facility) {
                Customer::factory()->count(5)->create([
                    'storage_facility_id' => $facility->id,
                ]);
            });
        });
    }
}
