<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGoalContributionsTable extends Migration
{
    public function up()
    {
        Schema::create('goal_contributions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('financial_goal_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // contributor (bisa beda user, misal hadiah)
            $table->decimal('amount', 12, 2);
            $table->text('note')->nullable();
            $table->date('contribution_date')->default(DB::raw('CURRENT_DATE'));
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('goal_contributions');
    }
}