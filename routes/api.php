<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\BudgetController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\CategoryBudgetController;
use App\Http\Controllers\API\ExpenseController;
use App\Http\Controllers\API\IncomeController;
use App\Http\Controllers\API\IncomeSourceController;

// 🔓 Public
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// 🔒 Protected
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    // Budgets
    Route::get('/budgets', [BudgetController::class, 'index']);
    Route::post('/budgets', [BudgetController::class, 'store']);
    Route::get('/budgets/{budget}', [BudgetController::class, 'show']);
    Route::put('/budgets/{budget}', [BudgetController::class, 'update']);
    Route::patch('/budgets/{budget}', [BudgetController::class, 'update']);
    Route::delete('/budgets/{budget}', [BudgetController::class, 'destroy']);

    // Categories
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::post('/categories', [CategoryController::class, 'store']);
    Route::get('/categories/{category}', [CategoryController::class, 'show']);
    Route::put('/categories/{category}', [CategoryController::class, 'update']);
    Route::patch('/categories/{category}', [CategoryController::class, 'update']);
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy']);

    // Income Sources
    Route::get('/income-sources', [IncomeSourceController::class, 'index']);
    Route::post('/income-sources', [IncomeSourceController::class, 'store']);
    Route::get('/income-sources/{incomeSource}', [IncomeSourceController::class, 'show']);
    Route::put('/income-sources/{incomeSource}', [IncomeSourceController::class, 'update']);
    Route::patch('/income-sources/{incomeSource}', [IncomeSourceController::class, 'update']);
    Route::delete('/income-sources/{incomeSource}', [IncomeSourceController::class, 'destroy']);

    // Incomes
    Route::get('/incomes', [IncomeController::class, 'index']);
    Route::post('/incomes', [IncomeController::class, 'store']);
    Route::get('/incomes/{income}', [IncomeController::class, 'show']);
    Route::put('/incomes/{income}', [IncomeController::class, 'update']);
    Route::patch('/incomes/{income}', [IncomeController::class, 'update']);
    Route::delete('/incomes/{income}', [IncomeController::class, 'destroy']);

    // Expenses
    Route::get('/expenses', [ExpenseController::class, 'index']);
    Route::post('/expenses', [ExpenseController::class, 'store']);
    Route::get('/expenses/{expense}', [ExpenseController::class, 'show']);
    Route::put('/expenses/{expense}', [ExpenseController::class, 'update']);
    Route::patch('/expenses/{expense}', [ExpenseController::class, 'update']);
    Route::delete('/expenses/{expense}', [ExpenseController::class, 'destroy']);

    // Category Budgets
    Route::post('/budgets/{budget}/allocations', [CategoryBudgetController::class, 'store']);
    Route::put('/allocations/{categoryBudget}', [CategoryBudgetController::class, 'update']);
    Route::patch('/allocations/{categoryBudget}', [CategoryBudgetController::class, 'update']);
    Route::delete('/allocations/{categoryBudget}', [CategoryBudgetController::class, 'destroy']);
});