<?php

namespace Database\Factories;

use App\Models\CategoryType;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryTypeFactory extends Factory
{
    protected $model = CategoryType::class;

    public function definition(): array
    {
        return ['type_name' => $this->faker->randomElement(['income', 'expense'])];
    }

    public function income(): static { return $this->state(fn () => ['type_name' => 'income']); }
    public function expense(): static { return $this->state(fn () => ['type_name' => 'expense']); }
}