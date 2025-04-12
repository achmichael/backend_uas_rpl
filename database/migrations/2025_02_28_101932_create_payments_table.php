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
        Schema::create('payments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('contract_id');
            $table->unsignedBigInteger('payment_method_id');
            $table->timestamp('payment_date')->useCurrent();
            $table->float('amount');
            $table->string('transaction_id)')->unique(); // for store transaction id from provider payments that id use for callback data payments
            $table->enum('payment_status', ['completed', 'pending', 'failed']);
            $table->timestamps();
            $table->foreign('contract_id')->references('id')->on('contracts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
