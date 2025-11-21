<?php

namespace Database\Factories;

use App\Models\IncomeSource;
use Illuminate\Database\Eloquent\Factories\Factory;

class IncomeSourceFactory extends Factory
{
    protected $model = IncomeSource::class;

    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'source_name' => $this->faker->randomElement(['Gaji', 'Bonus', 'Freelance', 'Investasi']),
        ];
    }

    public function salary(): static
    {
        return $this->state(fn () => ['source_name' => 'Gaji']);
    }
}