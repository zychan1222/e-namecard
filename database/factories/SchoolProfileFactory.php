<?php

namespace Database\Factories;

use App\Models\Country;
use App\Models\State;
use Illuminate\Database\Eloquent\Factories\Factory;

class SchoolProfileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name->en' => fake()->name,
            'name->zh' => fake()->name,
            'code' => uniqid(),
            'short_name' => fake()->name,
            'address' => fake()->address,
            'country_id' => Country::factory()->create()->id,
            'state_id' => State::factory()->create()->id,
            'city' => fake()->city,
            'postcode' => fake()->postcode,
            'phone_1' => fake()->phoneNumber,
            'phone_2' => fake()->phoneNumber,
            'fax_1' => fake()->phoneNumber,
            'fax_2' => fake()->phoneNumber,
            'email' => fake()->email,
            'url' => fake()->url,
        ];
    }
}
