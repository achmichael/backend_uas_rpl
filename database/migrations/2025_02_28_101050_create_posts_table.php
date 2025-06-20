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
        Schema::create('posts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('posted_by');
            $table->string('title', 150);
            $table->text('description');
            $table->float('price');
            $table->json('benefits')->nullable(); // Array: ["Health Insurance", "Gym Membership"]
            $table->json('requirements')->nullable(); // Array: ["Bachelor's Degree", "3+ Years Experience"]
            $table->unsignedBigInteger('level_id');
            $table->json('required_skills'); // Array: ["Laravel", "React"]
            $table->integer('min_experience_years');
            $table->enum('status', ['open','closed', 'draft']);
            $table->unsignedBigInteger('category_id');
            $table->timestamps();

            $table->foreign('level_id')->references('id')->on('levels')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->foreign('posted_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
