<?php

namespace Database\Factories;

use App\Models\Budget;
use Illuminate\Database\Eloquent\Factories\Factory;

class BudgetFactory extends Factory
{
    protected $model = Budget::class;

    public function definition(): array
    {
        $startDate = $this->faker->dateTimeBetween('now', '+30 days');
        $endDate = clone $startDate;
        $endDate->modify('+'. $this->faker->numberBetween(7, 30) .' days');

        return [
            'user_id' => \App\Models\User::factory(),
            'budget_name' => $this->faker->words(2, true) . ' ' . $startDate->format('M Y'),
            'total_amount' => $this->faker->numberBetween(1_000_000, 10_000_000),
            'start_date' => $startDate,
            'end_date' => $endDate,
        ];
    }
}