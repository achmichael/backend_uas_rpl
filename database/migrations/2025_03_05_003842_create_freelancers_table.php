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
        Schema::create('freelancers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->text('description');
            $table->uuid('user_id');
            $table->json('skills'); // Array: ["Laravel", "React"]
            $table->integer('experience_years');
            $table->json('educations'); // Array: [{"degree": "Bachelor", "field": "Computer Science", "institution": "XYZ University", "year": 2020}]
            $table->json('experiences'); // Array: [{"company": "ABC Corp", "position": "Developer", "duration": "2 years", "description": "Worked on various projects"}]
            $table->float('rating');
            $table->float('salary');
            $table->uuid('portofolio_id');
            $table->unsignedBigInteger('category_id'); // Website Dev, Mobile Dev, dsb.
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->foreign('portofolio_id')->references('id')->on('portofolios')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('freelancers');
    }
};
