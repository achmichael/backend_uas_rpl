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
        Schema::create('employees_companies', function (Blueprint $table) {
            $table->id();
            $table->uuid('company_id');
            $table->uuid('employee_id');
            $table->string('position', 100);
            $table->enum('status', [
                'active',
                'inactive',
                'cuty',
                'resigned'
            ]);
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('employee_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees_companies');
    }
};
