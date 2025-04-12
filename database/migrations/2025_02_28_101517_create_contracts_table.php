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
        Schema::create('contracts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('contract_type');
            $table->uuid('contract_type_id'); 
            $table->uuid('provider_id');
            $table->timestamp('contract_date')->useCurrent();
            $table->enum('status', ['active', 'completed', 'terminated', 'pending'])->default('pending');
            $table->timestamps();
            
            $table->foreign('provider_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};
