<?php

namespace Database\Factories;

use App\Models\Income;
use Illuminate\Database\Eloquent\Factories\Factory;

class IncomeFactory extends Factory
{
    protected $model = Income::class;

    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'income_source_id' => \App\Models\IncomeSource::factory(),
            'category_id' => \App\Models\Category::factory()->state(['category_type_id' => function () {
                return \App\Models\CategoryType::factory()->income()->create()->id;
            }]),
            'amount' => $this->faker->numberBetween(500_000, 15_000_000),
            'date' => $this->faker->dateTimeBetween('-30 days', 'now'),
            'description' => $this->faker->sentence(4),
        ];
    }

    public function salary(): static
    {
        return $this->state(function (array $attributes) {
            $user = \App\Models\User::find($attributes['user_id']);
            return [
                'income_source_id' => $user ? $user->incomeSources()->firstOrCreate(
                    ['source_name' => 'Gaji']
                )->id : \App\Models\IncomeSource::factory()->salary()->create()->id,
                'category_id' => $user ? $user->categories()->firstOrCreate(
                    ['category_name' => 'Gaji'],
                    ['category_type_id' => \App\Models\CategoryType::factory()->income()->create()->id]
                )->id : \App\Models\Category::factory()->salary()->create()->id,
                'amount' => $this->faker->randomElement([5_000_000, 7_500_000, 10_000_000, 15_000_000]),
                'description' => 'Gaji bulanan',
            ];
        });
    }
}