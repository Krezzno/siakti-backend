<?php

namespace Database\Factories;

use App\Models\Expense;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExpenseFactory extends Factory
{
    protected $model = Expense::class;

    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'category_id' => \App\Models\Category::factory()->state(['category_type_id' => function () {
                return \App\Models\CategoryType::factory()->expense()->create()->id;
            }]),
            'budget_id' => \App\Models\Budget::factory(),
            'amount' => $this->faker->numberBetween(10_000, 500_000),
            'date' => $this->faker->dateTimeBetween('-30 days', 'now'),
            'description' => $this->faker->sentence(3),
        ];
    }

    public function food(): static
    {
        return $this->state(function (array $attributes) {
            $user = \App\Models\User::find($attributes['user_id']);
            return [
                'category_id' => $user ? $user->categories()->firstOrCreate(
                    ['category_name' => 'Makan'],
                    ['category_type_id' => \App\Models\CategoryType::factory()->expense()->create()->id]
                )->id : \App\Models\Category::factory()->food()->create()->id,
                'amount' => $this->faker->randomElement([25_000, 35_000, 50_000, 75_000]),
                'description' => $this->faker->randomElement([
                    'Makan siang',
                    'Beli snack',
                    'Makan malam',
                    'Jajan kopi'
                ]),
            ];
        });
    }

    public function transport(): static
    {
        return $this->state(function (array $attributes) {
            $user = \App\Models\User::find($attributes['user_id']);
            return [
                'category_id' => $user ? $user->categories()->firstOrCreate(
                    ['category_name' => 'Transport'],
                    ['category_type_id' => \App\Models\CategoryType::factory()->expense()->create()->id]
                )->id : \App\Models\Category::factory()->transport()->create()->id,
                'amount' => $this->faker->randomElement([10_000, 15_000, 20_000, 30_000]),
                'description' => $this->faker->randomElement([
                    'Ojek online',
                    'Bensin motor',
                    'Tiket KRL',
                    'Parkir'
                ]),
            ];
        });
    }
}