<?php

namespace Database\Factories;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class EmployeeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Employee::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'password' => Hash::make('password'), 
            'name_cn' => $this->faker->name,
            'designation' => $this->faker->jobTitle,
            'phone' => $this->faker->phoneNumber,
            'profile_pic' => '', 
            'department' => $this->faker->word,
            'company_name' => $this->faker->company,
            'is_active' => true, 
        ];
    }
}
