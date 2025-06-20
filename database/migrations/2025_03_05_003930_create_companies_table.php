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
        Schema::create('companies', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->text('description')->nullable();
            $table->string('slug')->unique(); // for SEO and URL
            $table->string('name');
            $table->json('social_links')->nullable(); // social media links
            $table->string('cover_image'); // banner in profile company
            $table->text('address');
            $table->string('industry');
            $table->string('website');
            $table->dateTime('founded_at')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
