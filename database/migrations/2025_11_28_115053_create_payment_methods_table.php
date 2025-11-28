<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentMethodsTable extends Migration
{
    public function up()
    {
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name'); // e.g., "BCA", "GoPay", "Cash"
            $table->enum('type', ['cash', 'bank', 'e-wallet'])->default('cash');
            $table->string('account_number')->nullable(); // untuk bank/e-wallet
            $table->string('bank_name')->nullable(); // opsional, jika type = bank
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('payment_methods');
    }
}