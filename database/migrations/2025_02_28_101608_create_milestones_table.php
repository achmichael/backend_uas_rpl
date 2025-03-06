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
        Schema::create('milestones', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('contract_id');
            $table->text('milestone_description');
            $table->timestamp('due_date');
            $table->float('payment_amount');
            $table->enum('status', ['pending', 'completed']);
            $table->timestamps();

            $table->foreign('contract_id')->references('id')->on('contracts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('milestones');
    }
};
