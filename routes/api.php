<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BudgetPlanController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ExpenseController;
use App\Http\Controllers\Api\IncomeController;
use App\Http\Controllers\Api\IncomeSourceController;
use App\Http\Controllers\Api\PaymentMethodController;
use App\Http\Controllers\Api\FinancialGoalController;
use App\Http\Controllers\Api\GoalContributionController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// 🔓 Public
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// 🔒 Protected (auth:sanctum)
Route::middleware('auth:sanctum')->group(function () {

    // 🔐 Auth
    Route::post('/logout', [AuthController::class, 'logout']);

    // 💳 Payment Methods
    Route::apiResource('payment-methods', PaymentMethodController::class);

    // 🗂️ Categories
    Route::apiResource('categories', CategoryController::class);
    Route::get('categories/options', [CategoryController::class, 'options'])->name('categories.options');

    // 💰 Income Sources
    Route::apiResource('income-sources', IncomeSourceController::class)->except(['show']);

    // 💵 Incomes
    Route::apiResource('incomes', IncomeController::class);

    // 💸 Expenses
    Route::apiResource('expenses', ExpenseController::class);

    // 📊 Budget Plans (rencana per bulan/tahun)
    Route::apiResource('budget-plans', BudgetPlanController::class);

    // 🎯 Financial Goals
    Route::apiResource('goals', FinancialGoalController::class);

    // 💰 Goal Contributions (nested: /goals/{goalId}/contributions)
    Route::prefix('goals/{goalId}')->group(function () {
        Route::apiResource('contributions', GoalContributionController::class)
             ->except(['create', 'edit']); // resource tanpa create/edit
    });
    
});