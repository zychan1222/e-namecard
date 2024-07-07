<?php

namespace Database\Factories;

use App\Models\Admin;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class AdminFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Admin::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        // Get a random employee ID from the `employees` table
        $employee_id = Employee::inRandomOrder()->first()->id;

        return [
            'employee_id' => $employee_id,
            // You may add more fields here if necessary
        ];
    }
}
