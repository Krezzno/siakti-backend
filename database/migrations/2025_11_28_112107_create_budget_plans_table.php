<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateBudgetPlansTable extends Migration
{
    public function up()
    {
        Schema::create('budget_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('month'); // 1-12
            $table->integer('year');  // contoh: 2025
            $table->decimal('needs_budget', 10, 2)->default(0);
            $table->decimal('wants_budget', 10, 2)->default(0);
            $table->decimal('savings_budget', 10, 2)->default(0);
            $table->timestamps();
        });

        // Optional: tambahkan index untuk pencarian cepat
        Schema::table('budget_plans', function (Blueprint $table) {
            $table->index(['user_id', 'month', 'year']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('budget_plans');
    }
}