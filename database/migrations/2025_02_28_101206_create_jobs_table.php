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
        Schema::create('jobs_table', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('post_id');
            $table->integer('number_of_employee')->nullable()->default(0);
            $table->integer('duration');
            $table->enum('status', ['open','closed']);
            $table->enum('type_job',['full-time','part-time','contract']);
            $table->enum('type_salary',['fixed','flexible']);
            $table->enum('system',['wfo','wfh']);
            $table->timestamps();

            $table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jobs_table');
    }
};
