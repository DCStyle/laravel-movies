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
        Schema::create('movie_sources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('movie_id')->constrained()->onDelete('cascade');
            $table->enum('source_type', [
                'direct', 'fshare', 'gdrive',
                'youtube', 'twitter', 'facebook', 'tiktok'
            ]);
            $table->string('source_url');
            $table->enum('quality', ['360p', '480p', '720p', '1080p', '4k']);
            $table->boolean('is_primary')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movie_sources');
    }
};
