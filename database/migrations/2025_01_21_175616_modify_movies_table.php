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
        // Add column "title_en" to "movies" table
        Schema::table('movies', function (Blueprint $table) {
            $table->string('title_en')->nullable()->after('title');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop column "title_en" from "movies" table
        Schema::table('movies', function (Blueprint $table) {
            $table->dropColumn('title_en');
        });
    }
};
