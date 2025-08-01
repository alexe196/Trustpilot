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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->string('images')->nullable();
            $table->string('user_name');
            $table->integer('user_reviews_count')->nullable();
            $table->tinyInteger('rating');
            $table->string('title');
            $table->text('content');
            $table->string('review_date')->nullable();
            $table->string('experience_date')->nullable();
            $table->string('country')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
