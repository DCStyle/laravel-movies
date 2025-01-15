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
        Schema::table('movies', function (Blueprint $table) {
            $table->string('type')->default('single')->after('status');
            $table->integer('total_episodes')->nullable()->after('type');
            $table->integer('total_seasons')->nullable()->after('total_episodes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('movies', function (Blueprint $table) {
            $table->dropColumn(['type', 'total_episodes', 'total_seasons']);
        });
    }
};
