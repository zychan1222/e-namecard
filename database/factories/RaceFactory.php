<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class RaceFactory extends Factory
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
            'name->zh' => fake()->name
        ];
    }
}
