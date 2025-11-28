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
        Schema::create('incomes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                  ->constrained()
                  ->cascadeOnDelete();
            $table->foreignId('income_source_id')
                  ->constrained()
                  ->cascadeOnDelete();
            $table->foreignId('payment_method_id') // ✅ tambahkan ini
                  ->constrained()
                  ->cascadeOnDelete();
            $table->decimal('amount', 15, 2);
            $table->date('date');
            $table->text('description')->nullable(); // ✅ tambahkan
            $table->boolean('is_regular')->default(false); // ✅ rutin (gaji) vs insidental (bonus)
            $table->timestamps();
        });

        // Optional: tambahkan index untuk query cepat
        Schema::table('incomes', function (Blueprint $table) {
            $table->index(['user_id', 'date']);
            $table->index('income_source_id');
            $table->index('payment_method_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incomes');
    }
};