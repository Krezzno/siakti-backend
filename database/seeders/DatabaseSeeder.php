<?php

namespace Database\Seeders;

use App\Models\CategoryType;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::factory()->create([
            'name' => 'Pongo Backend',
            'email' => 'pongo.backend@siakti.com',
        ]);

        $incomeType = CategoryType::factory()->income()->create();
        $expenseType = CategoryType::factory()->expense()->create();

        $food = $user->categories()->create(['category_type_id' => $expenseType->id, 'category_name' => 'Makan']);
        $transport = $user->categories()->create(['category_type_id' => $expenseType->id, 'category_name' => 'Transport']);
        $salaryCat = $user->categories()->create(['category_type_id' => $incomeType->id, 'category_name' => 'Gaji']);

        $salarySource = $user->incomeSources()->create(['source_name' => 'Gaji']);

        $budget = $user->budgets()->create([
            'budget_name' => 'November 2025',
            'total_amount' => 5000000,
            'start_date' => '2025-11-01',
            'end_date' => '2025-11-30',
        ]);

        $budget->categoryBudgets()->create(['category_id' => $food->id, 'allocated_amount' => 2000000]);
        $budget->categoryBudgets()->create(['category_id' => $transport->id, 'allocated_amount' => 500000]);

        $user->incomes()->create([
            'income_source_id' => $salarySource->id,
            'category_id' => $salaryCat->id,
            'amount' => 10000000,
            'date' => '2025-11-05',
            'description' => 'Gaji November',
        ]);

        $user->expenses()->create([
            'category_id' => $food->id,
            'budget_id' => $budget->id,
            'amount' => 50000,
            'date' => '2025-11-06',
            'description' => 'Makan siang',
        ]);
    }
}