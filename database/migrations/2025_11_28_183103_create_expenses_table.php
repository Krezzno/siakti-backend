<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                  ->constrained()
                  ->cascadeOnDelete();
            $table->foreignId('category_id')
                  ->constrained()
                  ->cascadeOnDelete();
            $table->foreignId('payment_method_id')
                  ->constrained()
                  ->cascadeOnDelete();
            $table->decimal('amount', 15, 2);
            $table->date('date');
            $table->text('note')->nullable(); // sesuai ERD
            $table->timestamps();
        });

        // Index untuk performa query
        Schema::table('expenses', function (Blueprint $table) {
            $table->index(['user_id', 'date']);
            $table->index('category_id');
            $table->index('payment_method_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};