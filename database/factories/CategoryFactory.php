<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\CategoryType;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition(): array
    {
        return [
            'category_type_id' => CategoryType::factory(),
            'category_name' => $this->faker->word(),
        ];
    }

    public function food(): static
    {
        return $this->state(fn () => ['category_name' => 'Makan']);
    }

    public function transport(): static
    {
        return $this->state(fn () => ['category_name' => 'Transport']);
    }

    public function salary(): static
    {
        return $this->state(fn () => ['category_name' => 'Gaji']);
    }
}