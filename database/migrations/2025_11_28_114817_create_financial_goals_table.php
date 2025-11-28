<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFinancialGoalsTable extends Migration
{
    public function up()
    {
        Schema::create('financial_goals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('target_amount', 12, 2);
            $table->date('target_date')->nullable(); // opsional
            $table->enum('status', ['active', 'completed', 'canceled'])->default('active');
            $table->timestamps();
            $table->softDeletes(); // opsional: untuk soft delete
        });
    }

    public function down()
    {
        Schema::dropIfExists('financial_goals');
    }
}