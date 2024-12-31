<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('movie_ads', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['image', 'video']);
            $table->string('content_path');
            $table->float('display_time')->default(0); // percentage 0-100
            $table->integer('duration')->nullable(); // seconds for image ads
            $table->boolean('is_enabled')->default(true);
            $table->integer('order')->default(0);
            $table->string('click_url')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movie_ads');
    }
};
