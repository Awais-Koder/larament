<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => config('app.default_user.email')],
            [
                'name' => config('app.default_user.name'),
                'password' => Hash::make(config('app.default_user.password')),
            ]
        );
    }
}
