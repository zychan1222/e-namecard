<?php

namespace Database\Factories;

use App\Models\Employee;
use App\Models\User;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmployeeFactory extends Factory
{
    protected $model = Employee::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'company_id' => Organization::factory(),
            'name' => $this->faker->name,
            'name_cn' => $this->faker->name, 
            'designation' => $this->faker->jobTitle,
            'phone' => $this->faker->phoneNumber,
            'profile_pic' => $this->faker->imageUrl(), 
            'department' => $this->faker->word, 
            'is_active' => true,
        ];
    }
}
