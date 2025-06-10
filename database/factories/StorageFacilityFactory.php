<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StorageFacility>
 */
class StorageFacilityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'phone' => $this->faker->phoneNumber,
            'rsa_id' => $this->faker->numerify('##########'),
            'is_flagged' => false,
            'flagged_reason' => null,
            'user_id' => User::inRandomOrder()->first()?->id, // only if needed
            // Remove this line if you're not using facilities yet:
            // 'storage_facility_id' => null,
        ];
    }
}
