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
        // Add "crawl_source_url" column to "movies" table
        Schema::table('movies', function (Blueprint $table) {
            $table->string('crawl_source_url', 500)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop "crawl_source_url" column from "movies" table
        Schema::table('movies', function (Blueprint $table) {
            $table->dropColumn('crawl_source_url');
        });
    }
};
