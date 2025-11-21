<?php

namespace Database\Factories;

use App\Models\CategoryBudget;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryBudgetFactory extends Factory
{
    protected $model = CategoryBudget::class;

    public function definition(): array
    {
        return [
            'budget_id' => \App\Models\Budget::factory(),
            'category_id' => \App\Models\Category::factory(),
            'allocated_amount' => fn (array $attributes) => $this->faker->numberBetween(
                100_000,
                \App\Models\Budget::find($attributes['budget_id'])?->total_amount ?? 500_000
            ),
        ];
    }
} 