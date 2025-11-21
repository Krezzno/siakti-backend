<?php

namespace Database\Factories;

use App\Models\BudgetsSummary;
use Illuminate\Database\Eloquent\Factories\Factory;

class BudgetsSummaryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = BudgetsSummary::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'budget_id' => \App\Models\Budget::factory(),
            'total_expenses' => fn (array $attributes) => $this->faker->numberBetween(
                0,
                \App\Models\Budget::find($attributes['budget_id'])?->total_amount ?? 1_000_000
            ),
        ];
    }

    /**
     * Indicate that the summary has zero expenses (fresh budget).
     */
    public function fresh(): static
    {
        return $this->state(fn () => [
            'total_expenses' => 0,
        ]);
    }

    /**
     * Indicate that the summary reflects actual spending.
     */
    public function withExpenses(): static
    {
        return $this->state(function (array $attributes) {
            $budget = \App\Models\Budget::find($attributes['budget_id']);
            $max = $budget ? (int) $budget->total_amount : 1_000_000;
            return [
                'total_expenses' => $this->faker->numberBetween(0, $max),
            ];
        });
    }
}