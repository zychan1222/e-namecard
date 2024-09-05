<?php
namespace Database\Factories;

use App\Models\SocialConnection;
use Illuminate\Database\Eloquent\Factories\Factory;

class SocialConnectionFactory extends Factory
{
    protected $model = SocialConnection::class;

    public function definition()
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'provider' => 'twitter',
            'provider_id' => $this->faker->uuid,
            'access_token' => $this->faker->uuid,
        ];
    }
}